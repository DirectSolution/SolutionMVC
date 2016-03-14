<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Controller,
    SolutionORM;

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class HomeController extends Controller {

    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/home/index (which is the default page btw)
     */
    public function indexAction() {

        print "Loaded Homepage";
    }

    /**
     * PAGE: exampleone
     * This method handles what happens when you move to http://yourproject/home/exampleone
     * The camelCase writing is just for better readability. The method name is case-insensitive.
     */
    public function exampleOneAction($id) {

        

    }

    /**
     * PAGE: exampletwo
     * This method handles what happens when you move to http://yourproject/home/exampletwo
     * The camelCase writing is just for better readability. The method name is case-insensitive.
     */
    public function exampleTwoAction() {
        // load views
        // 
        print "two";
//        require APP . 'view/_templates/header.php';
//        require APP . 'view/home/example_two.php';
//        require APP . 'view/_templates/footer.php';
    }

}
