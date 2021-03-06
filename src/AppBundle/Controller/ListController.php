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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;



class ListController extends Controller{

	 /**
     * @Route("/manage_list", name="manage_list")
     */
    public function ListFormAndItemForm(Request $request){

        $current_user_id = $this->getUser()->getId();
        $list = new Listing();
        $form = $this->createForm(ListType::class, $list);
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:Listing')
            ->getList($current_user_id);
        return $this->render('manageList.html.twig', array(
            'ListForm' => $form->createView(),
            'ListArr'   => $result,
        ));
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
        	$list->setStatus(1);
        	$list->setSortOrder("");
        	$list->setColorCode("");
            $list->setUserID($this->getUser()->getId());
        	$errors = $validator->validate($list);
        	if(count($errors) > 0){
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
    
    /**
     * @Route("/add_child", name="add_child")
     */
    public function addChild(Request $request,ValidatorInterface $validator){

        $ItemName = $request->request->get('item');
        $sort = $request->request->get('sort');
        $parent_id = $request->request->get('parent_id');
        $color = $request->request->get('color');
        $entityManager = $this->getDoctrine()->getManager();
        $getParentRecord = $entityManager->getRepository(Listing::class)->find($parent_id);
      
        $Item = new Listing();
        $Item->setName($ItemName);
        $Item->setParent($getParentRecord);
        $Item->setStatus(1);
        $Item->setSortOrder($sort);
        $Item->setColorCode($color);
        $Item->setUserID($this->getUser()->getId());

        $errors = $validator->validate($Item);
        if(count($errors) > 0) {
            $errorsMsg = $errors[0]->getMessage();
            $response = array("output" => 'error','message' => $errorsMsg);
            return new JsonResponse($response);
        }
        $entityManager->persist($Item);
        $entityManager->flush();
        if($Item->getId()){
          $response = array("output" => 'success','message' => "Record Inserted into table successfully");
          return new JsonResponse($response);
        }else{
            $response = array("output" => 'error','message' => "Something Wrong");
          return new JsonResponse($response);
        }
    }

    /**
     * @Route("/remove_parent", name="remove_parent")
     */
    public function deleteParent(Request $request){
        
        $id = $request->request->get('del_id');
        $entityManager = $this->getDoctrine()->getManager(); 
        $parent = $entityManager->getRepository(Listing::class)->find($id);
        if($parent){
            $children = $entityManager->getRepository(Listing::class)->findBy([
            'parent' => $id]);
            if($children){
                foreach ($children as $val) {
                    $entityManager->remove($val);
                }
                $entityManager->flush();
            }    
            $entityManager->remove($parent);
            $entityManager->flush();
            $response = array("output" => 'success','message' => "Item Removed successfully"); 
        }else{
            $response = array("output" => 'success','message' => "No Item Found"); 
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/remove_child", name="remove_child")
     */
    public function deleteChild(Request $request){

        $id = $request->request->get('del_id');
        $entityManager = $this->getDoctrine()->getManager();
        $item = $entityManager->getRepository(Listing::class)->find($id);
         if (!$item) {
            $response = array("output" => 'error','message' => "No Such Item Found");
        }else{
        $entityManager->remove($item);
        $entityManager->flush();
        $response = array("output" => 'success','message' => "Item Removed successfully"); 
        }
        return new JsonResponse($response);
    }
}	