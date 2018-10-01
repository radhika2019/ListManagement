<?php

namespace AppBundle\Repository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Listing;
use Symfony\Component\HttpFoundation\Request;

/**
 * ListingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ListingRepository extends EntityRepository{
	/**
     * @return listArr[]
     */
	public function getList($user_id){

    	$query = $this->createQueryBuilder('Listing')
                ->select('Listing.name','Listing.id')
                ->where('Listing.status = 1','Listing.parent IS NULL','Listing.userID = :user')->setParameter('user', $user_id)
                ->getQuery();
                
        $listArr = $query->execute();
        $records = array();
        $master = array();
        $collect =array();
        foreach ($listArr as $keys => $value) {
            $childrens = array(); 
            $main = array();
            $childRecord = $this->createQueryBuilder('Listing')
                ->select('Listing.name','Listing.id')
                ->where('Listing.status = 1','Listing.parent = :parent')
                ->setParameter('parent', $value['id'])
                ->orderby('Listing.sortOrder','DESC')
                ->addorderby('Listing.id','DESC')
                ->getQuery();
            $itemArr = $childRecord->execute();
            $listArr[$keys]['itemsArr'] = $itemArr;
        }
        return $listArr;
    }

}
