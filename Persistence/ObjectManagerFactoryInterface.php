<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\Persistence;

interface ObjectManagerFactoryInterface
{
    /**
     * @param  string                 $class
     * @param  array                  $options
     * @return ObjectManagerInterface
     */
    public function create(array $options=array(), $class='');
}
