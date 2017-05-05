<?php

namespace AppBundle\Controller;

use AppBundle\Form\ProductFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    /**
     * @Route("product/details/{id}", name="product_details")
     */
    public function productDetailsAction($id) {
        return $this->render('shopping-cart/product-details.html.twig', [
            'id'      => $id
        ]);
    }

    /**
     * @Route("product/add", name="add_product")
     */
    public function addProductAction(Request $request)
    {
        $form = $this->createForm(ProductFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->getData()->getProductImage();
            $fileExtension = $file->guessExtension();
            $fileName = pathinfo($form->getData()->getProductImage()->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time().  '.' . $fileExtension;
            $file->move($this->getParameter('products_directory'), $fileName);
            $form->getData()->setProductImage($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('shopping-cart/add-product.html.twig', [
           'productForm'        => $form->createView()
        ]);
    }
}
