<?php
/**
 * Created by PhpStorm.
 * User: Family
 * Date: 5/3/2017
 * Time: 11:09 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ShoppingCartController extends Controller
{
    /**
     * @Route("add-to-cart/{productId}", name="add_to_cart")
     */
    public function addToCartAction($productId, Request $request)
    {
        $session = new Session();
        if (!$session->has('myCart')) {
            $session->set('myCart', [
                'product_id'    => [$productId],
                'count'         => [1]
            ]);
        } else {
            $arr = $session->get('myCart');

            for ($i =0, $len = count($arr['product_id']); $i < $len; $i++) {
                if (in_array($productId, $arr['product_id'])) {
                    $index = getKeyOfArray($arr['product_id'], $productId);
                    $arr['count'][$index] = ++$arr['count'][$index];
                    break;
                } else {
                    array_push($arr['product_id'], $productId);
                    array_push($arr['count'], 1);
                    break;
                }
            }

            $session->set('myCart', $arr);
        }

//        dd($session->get('myCart'));

        return $this->render('blog/index.html.twig', [
            'productId' => $productId
        ]);
    }
}