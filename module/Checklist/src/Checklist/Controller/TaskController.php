<?php
namespace Checklist\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Checklist\Form\TaskForm;
use Checklist\Model\TaskEntity;
use Zend\View\Model\ViewModel;

class TaskController extends AbstractActionController
{
	
	public function getTaskMapper()
	{
		$ms = $this->getServiceLocator();
		return $ms->get('TaskMapper');	
	}
	
	public function indexAction()
	{
		$mapper = $this->getTaskMapper();
		try {
			$tasks = $mapper->fetchAll();
		}
		catch (\Exception $ex) {
			echo $ex->getMessage();
		}
// 		$view = new ViewModel();
// 		var_dump($view->getTemplate());
		return array('tasks' => $tasks);
	}
	
	public function addAction()
	{
		$form = new TaskForm();
		$task = new TaskEntity();
		$form->bind($task);
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				if($task->getCompleted() === null) $task->setCompleted(0);
				if($task->getCreated() === null) $task->setCreated(date('Y-m-d H:i:s'));
				if($task->getUpdated() == null) $task->setUpdated(date('Y-m-d H:i:s'));
				$this->getTaskMapper()->saveTask($task);
				return $this->redirect()->toRoute('task');
			} else {
// 				var_dump($form->getMessages());
// 				exit('invalid');
			}
		}
		//$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		//$submit_val = $this->translate('Add');
		$this->translate('Add');
		$form->get('submit')->setValue('Add');
		return array('form' => $form);
	}
	
	public function editAction()
	{
		$id = (int) $this->params('id');
		if(!$id) {
			$this->redirect()->toRoute('task', array('action' => 'add'));
		}
		
		$task = $this->getTaskMapper()->getTask($id);
		$form = new TaskForm();
		$form->bind($task);
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				$task->setUpdated(date('Y-m-d H:i:s'));
				$this->getTaskMapper()->saveTask($task);
				$this->redirect()->toRoute('task');
			}
		}

		$form->get('submit')->setValue('Save');
		$view = new ViewModel(array('form' => $form));
		$view->setTemplate('checklist/task/add');
		return $view;
	}
}