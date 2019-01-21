<?php

use PHubyTest\Model\User;
use PHuby\Attribute\PasswordAttr;
use PHubyTest\DBI;

class UserTest extends TestCase {

    public function testResourceFindAndRefresh() {
        $this->setDB();
        $user = User::find(1);
        $this->assertEquals(null, $user);
        $user = new User([
            'email' => 'adams@gmail.com',
            'password' => PasswordAttr::hash_password('password'),
            'first_name' => 'Tester'
        ]);
        $this->assertEquals(null, $user->id->get());
        $user->save();
        $this->assertEquals(1, $user->id->get());
        $user = User::find(1);
        $this->assertInstanceOf(User::class, $user);
        $user->first_name = 'New tester';
        $this->assertEquals($user->first_name->get(), 'New tester');
        $user->refresh();
        $this->assertEquals($user->first_name->get(), 'Tester');
    }

    public function testResourceUpdate() {
        $this->setDB();
        $user = $this->createUser();
        $this->assertEquals($user->first_name->get(), 'Tester');
        $user->first_name = 'New tester';
        $user->save();
        $dbUser = User::find($user->id->get());
        $this->assertEquals($user->id->get(), $dbUser->id->get());
    }

    public function testResourceDelete() {
        $this->setDB();
        $user = $this->createUser();
        $this->assertInstanceOf(User::class, User::find(1));
        $user->delete();
        $this->assertEquals(User::find(1), null);
    }


}