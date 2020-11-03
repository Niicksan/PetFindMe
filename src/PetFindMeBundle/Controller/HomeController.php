<?php

namespace PetFindMeBundle\Controller;

use PetFindMeBundle\Entity\Contact;
use PetFindMeBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PetFindMeBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('home/index.html.twig',
            ['articles' => $articles]
        );
    }

    /**
     * @Route("/found", name="home_found")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function foundAction()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(['isFound' => 'Намерен']);

        return $this->render('home/found.html.twig',
            ['articles' => $articles]
        );
    }

    /**
     * @Route("/lost", name="home_lost")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lostAction()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(['isFound' => "Изгубен"]);

        return $this->render('home/lost.html.twig',
            ['articles' => $articles]
        );
    }


    /**
     * @Route("/about", name="home_about")
     */
    public function aboutAction()
    {
        return $this->render('home/about.html.twig');
    }

    /**
     * @Route("/contacts", name="home_contacts")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() )
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('home/contacts.html.twig',
            array('form'=>$form->createView())
        );
    }
}
