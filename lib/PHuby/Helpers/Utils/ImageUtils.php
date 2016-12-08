<?php

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error\FileError;

class ImageUtils extends AbstractUtils implements FileTypeInterface {

  const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];
  const DPI_OPTIONS = [
    72, 96, 120, 160, 200, 240, 300
  ];

  // 1 dpi = 0.393701 pixel/cm;
  const ONE_DPI_PX_BY_CM_RATIO = 0.393701;

  // 1 pixel/cm = 2.54 dpi
  const ONE_PX_BY_CM_DPI_RATIO = 2.54;

  static function get_file_dimensions($filepath) {
    // Check if file exists and is readable
    // @todo
  }

  static function check_extension($filename) {
    return FilesUtils::check_file_extension($filename, self::ALLOWED_EXTENSIONS);
  }

  /**
   * This method converts pixel value to cm based on the provided dpi.
   * If dpi is not provided, it uses minimum dpi allowed.
   * 
   * array['px'] integer with number of pixels
   * array['dpi'] integer requested dpi to be used to calculate cm value
   * 
   * @param mixed[] Array $args (see above)
   * @return float representin cm value for the given dpi
   */
  static function px_to_cm(Array $args) {
    
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
  static function px_to_cm_by_dpi($px) {
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
   * array['px'] integer with number of pixels
   * array['cm'] float with the lenght of the artwork
   * 
   * @param mixed[] array $args (see above)
   * @return integer maxiumum dpi possible for given parameters
   */
  static function dpi_from_px_and_cm(Array $args) {    
    return ($args['px']/$args['cm']) * self::ONE_PX_BY_CM_DPI_RATIO;
  }

  protected static function get_min_dpi() {
    return self::DPI_OPTIONS[0];
  }

}