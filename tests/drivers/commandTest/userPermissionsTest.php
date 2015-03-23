<?php

/* 
 * Copyright (C) 2015 Pedro Pelaez <aaaaa976@gmail.com>
 * Sources https://github.com/PSF1/pharinix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

class userPermissionsTest extends PHPUnit_Framework_TestCase {
    
    public static function setUpBeforeClass() {
        error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);
        while (!is_file("etc/pharinix.config.php")) {
            chdir("../");
        }
        include_once 'etc/pharinix.config.php';
        include_once("usr/adodb/cmsapi.php");
        include_once("etc/drivers/tools.php");
        include_once("etc/drivers/command.php");
        include_once("etc/drivers/user.php");
        driverUser::sessionStart();
    }
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    public static function tearDownAfterClass() {
        driverUser::logOut();
    }

    // Sessions
    public function testSessionAutoStart_Guest() {
        driverUser::logOut();
        driverUser::sessionStart();
        $this->assertTrue($_SESSION["user_guest_id"] == $_SESSION["user_id"]);
    }
    
    public function testSessionLogin_Guest() {
        driverUser::logOut();
        driverUser::logIn("guest@localhost", "");
        $this->assertTrue($_SESSION["user_guest_id"] == $_SESSION["user_id"]);
    }
    
    public function testSessionLogin_NoDataBase() {
        dbConn::$lockConnection = true; // Simulate no database connection
        $this->assertNotTrue(dbConn::haveConnection());
        $_SESSION = array(); // Reset session information
        driverUser::logOut();
        driverUser::logIn("guest@localhost", "");
        $this->assertTrue($_SESSION["user_guest_id"] == $_SESSION["user_id"]);
        $this->assertTrue($_SESSION["user_guest_id"] == 0);
        dbConn::$lockConnection = false;
    }
    // END Session
     
    // READ
    /**
     * Can owner read in each case?
     */
    public function testCanOwnerRead() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestRead(bindec($b1), true, false);
            if ($i == 0 || $i == 6) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    public function testCanOwnerRead_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestRead($i, true, false);
            if ((bool)($i & driverUser::PERMISSION_ALL_READ) || 
                (bool)($i & driverUser::PERMISSION_OWNER_READ)) {
                $this->assertTrue($can, decbin($i));
            } else {
                $this->assertNotTrue($can, decbin($i));
            }
        }
    }
    
    /**
     * Can group read in each case?
     */
    public function testCanGroupRead() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestRead(bindec($b1), false, true);
            if ($i == 3 || $i == 6) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    public function testCanGroupRead_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestRead($i, false, true);
            if ($i & driverUser::PERMISSION_ALL_READ || $i & driverUser::PERMISSION_GROUP_READ) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    /**
     * Can all read in each case?
     */
    public function testCanAllRead() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestRead(bindec($b1), false, false);
            if ($i == 6) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    /**
     * Can all read in each case?
     */
    public function testCanAllRead_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestRead($i, false, false);
            if ($i & driverUser::PERMISSION_ALL_READ) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    // END READ
    // WRITE
    /**
     * Can owner WRITE in each case?
     */
    public function testCanOwnerWRITE() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestWrite(bindec($b1), true, false);
            if ($i == 1 || $i == 7) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    public function testCanOwnerWRITE_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestWrite($i, true, false);
            if ((bool)($i & driverUser::PERMISSION_ALL_WRITE) || 
                (bool)($i & driverUser::PERMISSION_OWNER_WRITE)) {
                $this->assertTrue($can, decbin($i));
            } else {
                $this->assertNotTrue($can, decbin($i));
            }
        }
    }
    
    /**
     * Can group WRITE in each case?
     */
    public function testCanGroupWRITE() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestWrite(bindec($b1), false, true);
            if ($i == 4 || $i == 7) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    public function testCanGroupWRITE_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestWrite($i, false, true);
            if ($i & driverUser::PERMISSION_ALL_WRITE || $i & driverUser::PERMISSION_GROUP_WRITE) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    /**
     * Can all WRITE in each case?
     */
    public function testCanAllWRITE() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestWrite(bindec($b1), false, false);
            if ($i == 7) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    /**
     * Can all WRITE in each case?
     */
    public function testCanAllWRITE_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestWrite($i, false, false);
            if ($i & driverUser::PERMISSION_ALL_WRITE) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    // END WRITE
    // EXECUTE
    /**
     * Can owner EXECUTE in each case?
     */
    public function testCanOwnerEXECUTE() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestExecute(bindec($b1), true, false);
            if ($i == 2 || $i == 8) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    public function testCanOwnerEXECUTE_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestExecute($i, true, false);
            if ((bool)($i & driverUser::PERMISSION_ALL_EXECUTE) || 
                (bool)($i & driverUser::PERMISSION_OWNER_EXECUTE)) {
                $this->assertTrue($can, decbin($i));
            } else {
                $this->assertNotTrue($can, decbin($i));
            }
        }
    }
    
    /**
     * Can group EXECUTE in each case?
     */
    public function testCanGroupEXECUTE() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestExecute(bindec($b1), false, true);
            if ($i == 5 || $i == 8) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    public function testCanGroupEXECUTE_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestExecute($i, false, true);
            if ($i & driverUser::PERMISSION_ALL_EXECUTE || $i & driverUser::PERMISSION_GROUP_EXECUTE) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    /**
     * Can all EXECUTE in each case?
     */
    public function testCanAllEXECUTE() {
        $b = "000000000";
        for($i = 8; $i >= 0; --$i) {
            $b1 = $b;
            $b1[$i] = 1;
            $can = driverUser::secTestExecute(bindec($b1), false, false);
            if ($i == 8) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    
    /**
     * Can all EXECUTE in each case?
     */
    public function testCanAllEXECUTE_Combined() {
        for($i = 0; $i < 512; ++$i) {
            $can = driverUser::secTestExecute($i, false, false);
            if ($i & driverUser::PERMISSION_ALL_EXECUTE) {
                $this->assertTrue($can);
            } else {
                $this->assertNotTrue($can);
            }
        }
    }
    // END EXECUTE
    public function testCanPerformance() {
        $stime = microtime(TRUE);
        for($i = 0; $i < 1000000; ++$i) {
            driverUser::secTestExecute(511, false, false);
        }
        $etime = microtime(TRUE);
        $c = ($etime - $stime);
        $this->assertLessThan(7, $c);
    }
}