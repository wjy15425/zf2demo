<?php
namespace Album\Controller;

use Zend\Mvc\controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AlbumController extends AbstractActionController{
    protected $albumTable;
    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }
    public function editAction()
    {
    	echo __FUNCTION__;die;
    }
    public function addAction()
    {
    	echo __FUNCTION__;die;
    }
    public function infoAction()
    {
        echo __FUNCTION__;die;
    }
    public function deleteAction()
    {
    	echo __FUNCTION__;die;
    }
}
