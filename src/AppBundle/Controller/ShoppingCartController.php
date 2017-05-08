<?php
/**
 * Created by PhpStorm.
 * User: Family
 * Date: 5/5/2017
 * Time: 05:40 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ShoppingCartController extends Controller
{
    /**
     * This function used to add new item into shopping cart
     * @Route("add-to-cart/{productId}", name="add_to_cart")
     */
    public function addToCartAction($productId)
    {
        $session = new Session();

        if (!$session->has('myCart')) {
            $session->set('myCart', [
                'product_id'    => [$productId],
                'count'         => [1]
            ]);
        } else {
            $myCart = $session->get('myCart');
            if (!count($myCart['product_id'])) {
                $session->set('myCart', [
                    'product_id'    => [$productId],
                    'count'         => [1]
                ]);
                return new Response(array_sum($session->get('myCart')['count']));
            }
            for ($i =0, $len = count($myCart['product_id']); $i < $len; $i++) {
                if (in_array($productId, $myCart['product_id'])) {
                    $index = getKeyOfArray($myCart['product_id'], $productId);
                    $myCart['count'][$index] = ++$myCart['count'][$index];
                    break;
                } else {
                    array_push($myCart['product_id'], $productId);
                    array_push($myCart['count'], 1);
                    break;
                }
            }

            $session->set('myCart', $myCart);
        }

        return new Response(array_sum($session->get('myCart')['count']));
    }

    /**
     * Used to display new page that contained all details in your shopping cart
     * @Route("display-my-cart", name="display_my_cart")
     */
    public function displayMyShoppingCart()
    {
        $session = new Session();
        if (!$session->has('myCart')) {
            return $this->redirectToRoute('homepage');
        }

        $myItems = $this->getDoctrine()->getRepository('AppBundle:Product');
        $query = $myItems->createQueryBuilder('p')
                         ->andWhere('p.id IN (:productID)')
                         ->setParameter('productID', $session->get('myCart')['product_id'])
                         ->getQuery();

        $currentCart = getCurrentCart($query->getResult(), $session->get('myCart')['product_id'], $session->get('myCart')['count']);

        if (is_null($currentCart)) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('shopping-cart/display-my-shopping-cart.html.twig', [
            'myItems'   => $query->getResult(),
            'currentCart'  => $currentCart,
            'totalAmount'   => $this->getTotalCost($currentCart, $query->getResult()),
            'totalCountOfItems' => getTotalCountOfItems()
        ]);
    }

    /**
     * Used to return total cost by using 2 parameters the first array $currentCart [the key should contain productId and the value contain the count of         * times this product is requested from the current user] and the second array should contain  $query->getResult()
     * @param array $currentCart
     * @param array $products
     * @return int
     */
    private function getTotalCost(Array $currentCart, Array $products) {
        for ($i = 0, $len = count($products), $totalAmount = 0; $i < $len; $i++) {
            $totalAmount += ($currentCart[$products[$i]->getId()] > $products[$i]->getProductQuantity()) ? (0 * $products[$i]->getProductPrice()) : ($currentCart[$products[$i]->getId()] * $products[$i]->getProductPrice());
        }
        return $totalAmount;
    }

    /**
     * Used to return the price for a specific productId from database
     * @param $productId
     * @param array $products
     * @return int|float
     */
    private function getPriceForProduct($productId, Array $products) {
        for ($i = 0, $len = count($products); $i < $len; $i++) {
            if ($products[$i]->getId() == $productId) {
                return $products[$i]->getProductPrice();
            }
        }
    }

    /**
     * Used to return the quantity for a specific productId from database
     * @param $productId
     * @param array $products
     * @return int
     */
    private function getQuantityForProduct($productId, Array $products) {
        for ($i = 0, $len = count($products), $totalAmount = 0; $i < $len; $i++) {
            if ($products[$i]->getId() == $productId) {
                return $products[$i]->getProductQuantity();
            }
        }
    }

    /**
     * Used to remove all productId from session
     * @Route("empty-my-cart", name="empty_my_cart")
     */
    public function emptyMyCart()
    {
        $session = new Session();
        $session->remove('myCart');
        return new Response(null);
    }

    /**
     * Used to remove Product from user shopping cart
     * @Route("remove-product/{productId}", name="remove_product")
     */
    public function removeProductFromCart($productId)
    {
        $session = new Session();
        $arrayOfProductId = $session->get('myCart')['product_id'];
        $arrayOfCount = $session->get('myCart')['count'];
        unset($arrayOfProductId[getKeyOfArray($session->get('myCart')['product_id'], $productId)]);
        unset($arrayOfCount[getKeyOfArray($session->get('myCart')['product_id'], $productId)]);
        $session->remove('myCart');
        $session->set('myCart', [
            'product_id'    => getNewArrayAfterUnset($arrayOfProductId),
            'count'         => getNewArrayAfterUnset($arrayOfCount)
        ]);

        $getResult = $this->getProducts($session);

        if ($getResult) {
            $totalAmount = $this->getTotalCost(
                getCurrentCart($getResult, $session->get('myCart')['product_id'], $session->get('myCart')['count'])
                , $getResult);
            return new JsonResponse([
                'totalAmount'   => $totalAmount,
                'totalCountOfItems' => getTotalCountOfItems()
            ]);
        }
        return new JsonResponse([
            'totalAmount'   => 0,
            'totalCountOfItems' => getTotalCountOfItems()
        ]);
    }

    /**
     * This function will return all data about all products id has been added into user shopping cart
     * @param $session
     * @return array
     */
    private function getProducts($session) {
        $myItems = $this->getDoctrine()->getRepository('AppBundle:Product');
        $query = $myItems->createQueryBuilder('p')
                         ->andWhere('p.id IN (:productID)')
                         ->setParameter('productID', $session->get('myCart')['product_id'])
                         ->getQuery();
        return $query->getResult();
    }

    /**
     * Used to give the user ability to change the quantity of any items in his shopping cart
     * @Route("change-user-quantity/{productId}", name="change_user_quantity")
     */
    public function changeUserQuantity($productId, Request $request)
    {
        $session = new Session();
        $arr = $session->get('myCart')['count'];
        $arr[getKeyOfArray($session->get('myCart')['product_id'], $productId)] = $request->request->get('quantity');
        $session->set('myCart', [
            'product_id'    => $session->get('myCart')['product_id'],
            'count'         => $arr
        ]);
        $getResult = $this->getProducts($session);
        $totalAmount = 0;
        if ($getResult) {
            $totalAmount = $this->getTotalCost(
                getCurrentCart($getResult, $session->get('myCart')['product_id'], $session->get('myCart')['count'])
                , $getResult);
        }

        $response = [
            'quantity'  => $request->request->get('quantity'),
            'totalCountOfItems' => getTotalCountOfItems(),
            'totalAmount'   =>  $totalAmount,
            'productPrice'  => $this->getPriceForProduct($productId, $getResult),
            'productQuantity'   => $this->getQuantityForProduct($productId, $getResult)
        ];
        return new JsonResponse($response);
    }
}