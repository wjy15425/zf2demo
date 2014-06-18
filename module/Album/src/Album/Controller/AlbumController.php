<?php
namespace Album\Controller;

use Zend\Mvc\controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;

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
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if(!$id) {
    		return $this->redirect()->toRoute('halbum/default', array(
    			'action' => 'add'
    		));
    	}
    	
    	try {
    		$album = $this->getAlbumTable()->getAlbum($id);
    	}
    	catch (\Exception $ex) {
    		return $this->redirect()->toRoute('halbum/default', array(
    				'action' => 'index'
    		));
    	}
    	//var_dump($album);die;
    	$form  = new AlbumForm();
    	$form->bind($album);
    	$form->get('submit')->setAttribute('value', 'Edit');
    	
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($album->getInputFilter());
    		$form->setData($request->getPost());
    	
    		if ($form->isValid()) {
    			$this->getAlbumTable()->saveAlbum($album);
    	
    			// Redirect to list of albums
    			return $this->redirect()->toRoute('halbum');
    		}
    	}
    	
    	return array(
    			'id' => $id,
    			'form' => $form,
    	);
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
