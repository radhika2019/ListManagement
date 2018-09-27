<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\ListType;


class ListController extends Controller{
	 /**
     * @Route("/manage_list", name="manage_list")
     */
    public function ListFormAndItemForm(Request $request){
        $list = new Listing();
        $form = $this->createForm(ListType::class, $list);

        $form->handleRequest($request);

        return $this->render('manageList.html.twig', array(
            'ListForm' => $form->createView()
        ));
       // return $this->render('manageList.html.twig');
    }
    /**
     * @Route("/add_parent", name="add_parent")
     */
    public function addParent(Request $request,ValidatorInterface $validator){

    	if($request->isXmlHttpRequest()) {
    		$listName = $request->request->get('name');
    		$entityManager = $this->getDoctrine()->getManager();
    		$list = new Listing();
        	$list->setName($listName);
        	$list->setParentId(0);
        	$list->setStatus(1);
        	$list->setSortOrder("");
        	$list->setColorCode("");
            $list->setUserID($this->getUser()->getId());
            //$list->setUserID($this->getUser()->getId());
        	$errors = $validator->validate($list);
        	if(count($errors) > 0) {
        	 	$errorsMsg = $errors[0]->getMessage();
        	 	$response = array("output" => 'error','message' => $errorsMsg);
        		return new JsonResponse($response);
        	}
        	$entityManager->persist($list);
        	$entityManager->flush();
        	if($list->getId()){
        	  $response = array("output" => 'success','message' => "Record Inserted into table successfully");
			  return new JsonResponse($response);
			}else{
				$response = array("output" => 'error','message' => "Something Wrong");
			  return new JsonResponse($response);
			}
    	}else{
    		$response = array("output" => 'error','message' => "Something Wrong");
			return new JsonResponse($response);
    	}
    }
}	