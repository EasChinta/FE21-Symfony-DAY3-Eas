<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\Blog;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        $blog = $this->getDoctrine()->getRepository('App:Blog')->findAll();

        return $this->render('blog/index.html.twig', array('blog' => $blog));
    }

    #[Route("/create", name: "blog_create")]
    public function create(Request $request): Response
    {
        // Here we create an object from the class that we made
        $blog = new Blog;
        /* Here we will build a form using createFormBuilder and inside this function we will put our object and then we write add then we select the input type then an array to add an attribute that we want in our input field */
        $form = $this->createFormBuilder($blog)->add('title', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('picture', UrlType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Create Blog', 'attr' => array('class' => 'btn-grad', 'style' => 'margin-bottom:15px')))
            ->getForm();
        $form->handleRequest($request);


        /* Here we have an if statement, if we click submit and if  the form is valid we will take the values from the form and we will save them in the new variables */
        if ($form->isSubmitted() && $form->isValid()) {
            //fetching data
            // taking the data from the inputs by the name of the inputs then getData() function
            $title = $form['title']->getData();
            $picture = $form['picture']->getData();
            $description = $form['description']->getData();


            /* these functions we bring from our entities, every column have a set function and we put the value that we get from the form */
            $blog->setTitle($title);
            $blog->setPicture($picture);
            $blog->setDescription($description);
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();
            $this->addFlash(
                'notice',
                'Blog Added'
            );
            return $this->redirectToRoute('blog');
        }
        /* now to make the form we will add this line form->createView() and now you can see the form in create.html.twig file  */
        return $this->render('blog/create.html.twig', array('form' => $form->createView()));
    }

    #[Route("/edit/{id}", name: "blog_edit")]
    public function edit(Request $request, $id): Response
    {
        $blog = $this->getDoctrine()->getRepository('App:Blog')->find($id);
       

        /* Now when you type createFormBuilder and you will put the variable todo the form will be filled of the data that you already set it */
        $form = $this->createFormBuilder($blog)->add('title', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('picture', UrlType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Update Blog', 'attr' => array('class' => 'btn-grad', 'style' => 'margin-bottom:15px', 'onclick' => "return confirm('Do you want to save the changes?')" )))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //fetching data
            $title = $form['title']->getData();
            $picture = $form['picture']->getData();
            $description = $form['description']->getData();

            $em = $this->getDoctrine()->getManager();
            $blog = $em->getRepository('App:Blog')->find($id);

            $blog->setTitle($title);
            $blog->setPicture($picture);
            $blog->setDescription($description);

            $em->flush();
            $this->addFlash(
                'notice',
                'Blog Updated'
            );
            return $this->redirectToRoute('blog');
        }
        return $this->render('blog/edit.html.twig', array('blog' => $blog, 'form' => $form->createView()));
    }

    #[Route("/details/{id}", name: "blog_details")]
    public function details($id): Response
    {
        $blog = $this->getDoctrine()->getRepository('App:Blog')->find($id);
        return $this->render('blog/details.html.twig', array('blog' => $blog));
    }

    #[Route("/delete/{id}", name: "blog_delete")]
    public function delete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('App:Blog')->find($id);
        $em->remove($blog);
       
        $em->flush();
        $this->addFlash(
            'notice',
            'Blog Removed'
        );
       
        return $this->redirectToRoute('blog');
    }
}
