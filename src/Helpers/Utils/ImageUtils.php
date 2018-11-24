<?php
/**
 * ImageUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error\FileError;
use PHuby\Error\MissingParameterError;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\Logger;

class ImageUtils extends AbstractUtils implements FileTypeInterface {

  const 
    ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'],
    DPI_OPTIONS = [
      72, 96, 120, 160, 200, 240, 300
    ],
    DEFAULT_IMG_QUALITY = 100;


  // 1 dpi = 0.393701 pixel/cm;
  const ONE_DPI_PX_BY_CM_RATIO = 0.393701;

  // 1 pixel/cm = 2.54 dpi
  const ONE_PX_BY_CM_DPI_RATIO = 2.54;

  /**
   * Method gets the size of an image
   * 
   * @param string $str_image_path representing an absolute path to the file
   * @return mixed[] Array containing image width and height
   * @todo check if the file is an image and is readable
   */
  public static function get_image_size($str_image_path) {
    return getimagesize($str_image_path);
  }

  public static function check_extension($filename) {
    return FileUtils::check_file_extension($filename, self::ALLOWED_EXTENSIONS);
  }

  /**
   * This method converts pixel value to cm based on the provided dpi.
   * If dpi is not provided, it uses minimum dpi allowed.
   * 
   * * array['px'] integer with number of pixels
   * * array['dpi'] integer requested dpi to be used to calculate cm value
   * 
   * @param mixed[] Array $args (see above)
   * @return float representin cm value for the given dpi
   */
  public static function px_to_cm(array $args) {
    
    // Set minimum DPI as a default
    $dpi = self::get_min_dpi();
    
    // Check if there is a specific DPI passed, and if so, overwrite $dpi
    if(isset($args['dpi'])) {
      $dpi = $args['dpi'];      
    }

    // Perform the calculation, to get the maximum size in the specified DPI
    return number_format($args['px']/($dpi*self::ONE_DPI_PX_BY_CM_RATIO), 2);
  }

  /**
   * Method converts pixel value to cm and group it by dpi steps
   * 
   * @param integer $px amount of pixels
   * @return mixed[] array where key is a dpi and value is cm in that dpi
   */
  public static function px_to_cm_by_dpi($px) {
    // Create temporary array to hold the data
    $cm_by_dpi = [];

    // Foreach dpi option, convert 
    foreach(self::DPI_OPTIONS as $dpi) {
      $cm_by_dpi[$dpi] = self::px_to_cm([
          'px' => $px,
          'dpi' => $dpi
        ]);
    }

    return $cm_by_dpi;
  }

  /**
   * Method aims to find the maximum DPI for the artwork with certain amount of pixels
   * that is desired to be x cm long
   * 
   * * array['px'] integer with number of pixels
   * * array['cm'] float with the lenght of the artwork
   * 
   * @param mixed[] array $args (see above)
   * @return integer maxiumum dpi possible for given parameters
   */
  public static function dpi_from_px_and_cm(array $args) {    
    return ($args['px']/$args['cm']) * self::ONE_PX_BY_CM_DPI_RATIO;
  }
  
  protected static function get_min_dpi() {
    return self::DPI_OPTIONS[0];
  }

  /**
   * Method calculates new size of the image based on given parameters.
   * It keeps the ratio of the old parameters, and uses new dimensions as thresholds
   * 
   * * array['current_width']     int containing current width in px
   * * array['current_height']    int containing current height in px
   * * array['max_width']         int containing new max width in px
   * * array['max_height']        int containing new max height in px
   * 
   * @param   mixed[] $arr_params (see above)
   * @return  mixed[] containing new dimensions, with keys height and width
   * @throws  \PHuby\Error\MissingParameterError if any of the required parameter is misisng
   */
  public static function calculate_new_size(array $arr_params) {
    $arr_required_params = ['current_width', 'current_height', 'max_width', 'max_height'];
    if(ArrayUtils::keys_exist($arr_required_params, $arr_params)) {

      Logger::debug("Calulating new size of an image with following parameters: " . json_encode($arr_params));      
      // Assign passed parameters to variables
      foreach($arr_required_params as $param_name) {
        $$param_name = $arr_params[$param_name];
      }

      // Calculate ratio
      $ratio = $current_width/$current_height;

      // Start calculating new dimensions
      // Check width
      if($max_width >= $current_width) {
        // Width is ok. Check height
        if($max_height >= $current_height) {
          // Height is ok too
          $dimensions = [$current_width, $current_height];
        } else {
          // Picture too high. Adjust based on ratio
          // e.g. New height = 50, width = height * ratio
          $dimensions = [round($max_height*$ratio), $max_height];
        }
      } else {
        // Picture too wide
        if($max_height >= $current_height) {
          // Height is ok. adjust based on width.
          // e.g. width = 100, height = width / ratio
          $dimensions = [$max_width, round($max_width/$ratio)];
        } else {
          // Both height and width are too big
          // Get new dimensions ratio
          $max_ratio = $max_width / $max_height;
      
          if($max_ratio < $ratio) {
            // We need to adjust dimensions based on width
            $dimensions = [$max_width, $max_width/$ratio];
          } elseif($max_ratio == $ratio) {
            // Assign max values
            $dimensions = [$max_width, $max_height];
          } else {
            // Adjust based on height
            $dimensions = [$max_height*$ratio, $max_height];
          }
        }
      }

      // Once calculated, return new values as an array
      $arr_new_dimensions = [
        $dimensions[0],
        $dimensions[1]
      ];

      Logger::debug("Finished calculation with following dimesnions: " . json_encode($arr_new_dimensions));

      return $arr_new_dimensions;

    } else {
      throw new MissingParameterError("Unable to calculate new image size, as some parameters are missing. Got " . json_encode($arr_params));
    }
  }


  /**
   * Method resizes the image and makes a permanent change to it
   * 
   * * array['image_path']        string containing absolute path to the image
   * * array['max_width']         int containing new max width in px
   * * array['max_height']        int containing new max height in px
   * * array['quality']           int (optional) containing quality of the image (1 to 100)
   * 
   * @param mixed[] array $arr_params (see above)
   * @return boolean true if image has been resized
   * @throws \PHuby\Error\FileError if image is not readable
   * @throws \PHuby\Error\FileError if unable to resize image
   * @throws \PHuby\Error\MissingParameterError if any of the required parameter is misisng
   */
  public static function resize(array $arr_params) {
    $arr_required_params = ["image_path", "max_width", "max_height"];
    if(ArrayUtils::keys_exist($arr_required_params, $arr_params)) {

      // Now check if the file exists and is readable
      if(FileUtils::is_readable($arr_params["image_path"])) {

        // Get current height and width of the picture
        list($current_width, $current_height) = self::get_image_size($arr_params["image_path"]);
        
        list($new_width, $new_height) = self::calculate_new_size([
            "current_width" => $current_width,
            "current_height" => $current_height,
            "max_width" => $arr_params["max_width"],
            "max_height" => $arr_params["max_height"]
          ]);

        if(array_key_exists("quality", $arr_params)) {
          $quality = $arr_params["quality"];
        } else {
          $quality = self::DEFAULT_IMG_QUALITY;
        }

        // Once calculated, resize and save the image
        $image_copy = imagecreatefromjpeg($arr_params["image_path"]);
        $new_image  = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $image_copy, 0, 0, 0, 0, $new_width, $new_height, $current_width, $current_height);

        if(imagejpeg($new_image, $arr_params["image_path"], 100)) {
          // Image has been resized correctly
          return true;
        } else {
          throw new FileError("Unable to resize the image.");
        }

      } else {
        throw new FileError("File located in " . $arr_params["image_path"] . " is not readable");
      }

    } else {
      throw new MissingParameterError("Unable to resize image as method hasn't received all required parameters. Got: " . json_encode($arr_params). ". Require: " . json_encode($arr_required_params));
    }
  }

  /**
   * Method crops the picture using different source locations.
   * 
   * * array['image_path']        string containing absolute path to the image
   * * array['source_x']          int containing x px location to crop from
   * * array['source_y']          int containing y px location to crop from
   * * array['target_width']      int containing target width in px
   * * array['target_height']     int containing target height in px
   * * array['source_width']      int containing width in px of the source image
   * * array['source_height']     int containing height in px of the source image
   * * array['quality']           int (optional) containing quality of the image (1 to 100)
   * 
   * @param mixed[] array $arr_params (see above)
   * @return boolean true if picture has been cropped properly
   * @throws \PHuby\Error\MissingParameterError if any of the required parameter is misisng
   * @throws \PHuby\Error\FileError if image cannot be cropped
   * @throws \PHuby\Error\FileError if image cannot be resampled
   * @throws \PHuby\Error\FileError if image is not readable
   */
  public static function crop(array $arr_params) {
    $arr_required_params = ["image_path", "source_x", "source_y", "target_width", "target_height"];
    if(ArrayUtils::keys_exist($arr_required_params, $arr_params)) {

      // Check if the image is readeable
      if(FileUtils::is_readable($arr_params["image_path"])) {

        // In case cropping takes place
        $source_image = imagecreatefromjpeg($arr_params["image_path"]);
        $destination_image = imagecreatetruecolor($arr_params["target_width"], $arr_params["target_height"]);

        if(array_key_exists("quality", $arr_params)) {
          $quality = $arr_params["quality"];
        } else {
          $quality = self::DEFAULT_IMG_QUALITY;
        }

        if(imagecopyresampled(
            $destination_image,
            $source_image,
            0,
            0,
            $arr_params["source_x"],
            $arr_params["source_y"],
            $arr_params["target_width"],
            $arr_params["target_height"],
            $arr_params["source_width"],
            $arr_params["source_height"]
          )) {        

          if(imagejpeg($destination_image, $arr_params["image_path"], $quality)) {
            return true;
          } else {
            throw new FileError("Unable to crop the file.");
          }
        } else {
          throw new FileError("Unable to resample image.");
        }

      } else {
        throw new FileError("File located in " . $arr_params["image_path"] . " is not readable");
      }   

    } else {
      throw new MissingParameterError("Unable to resize image as method hasn't received all required parameters. Got: " . json_encode($arr_params). ". Require: " . json_encode($arr_required_params));     
    }
  }

}