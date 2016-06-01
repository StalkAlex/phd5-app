<?php

namespace app\controllers;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2016 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use dmstr\web\traits\AccessBehaviorTrait;
use Yii;
use yii\web\Controller;

/**
 * Site controller.
 */
class SiteController extends Controller
{
    use AccessBehaviorTrait;
    
    /**
     * Renders the start page.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
