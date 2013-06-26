<?php
namespace Qimnet\CRUDBundle\Table;

use Qimnet\TableBundle\Templating\AbstractTableRendererStrategyDecorator;
use Qimnet\CRUDBundle\HTTP\CRUDRequestInterface;
use Qimnet\CRUDBundle\Configuration\CRUDAction;

/*
 *  This file is part of Qimnet CRUD Bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

/**
 * Renders a link to a CRUD object
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CRUDLinkRendererStrategy extends AbstractTableRendererStrategyDecorator
{
    /**
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;
    public function setCRUDRequest(CRUDRequestInterface $CRUDRequest=null)
    {
        $this->CRUDRequest = $CRUDRequest;
    }

    public function canRender($value, array $options = array())
    {
        return true;
    }

    public function getName()
    {
        return 'crud_link';
    }

    public function getPriority()
    {
        return false;
    }

    public function render($value, array $options = array())
    {
        $configuration = $this->CRUDRequest->getConfiguration();
        $security = $configuration->getSecurityContext();
        $action = false;
        $actions = isset($options['actions']) ? $options['actions'] : array(CRUDAction::SHOW, CRUDAction::UPDATE);
        foreach ($actions as $_action) {
            if ($security->isActionAllowed($_action, $options['object'], $options['object_vars'])) {
                $action = $_action;
                break;
            }
        }
        if ($action) {
            $link = $configuration
                ->getPathGenerator()
                ->generate($action,
                        isset($options['link_parameters']) ? $options['link_parameters'] : array(),
                        $options['object'],
                        $options['object_vars']);

            return sprintf('<a href="%s">%s</a>', htmlspecialchars($link), $this->renderParent($value, $options));
        } else {
            return $this->renderParent($value, $options);
        }
    }
}