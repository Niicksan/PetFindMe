<?php


namespace PetFindMeBundle\Controller;


use PetFindMeBundle\Entity\Article;
use PetFindMeBundle\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/article/create", name="article_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $article->setAuthor($this->getUser());

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            $destination = $this->getParameter('kernel.project_dir').'/web/images';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME );
            $newFilename = $originalFilename.'.'.$uploadedFile->guessExtension();

            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $article->setImageFilename($newFilename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('article/create.html.twig',
            array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/myArticles/edit/{id}", name="article_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function edit(Request $request, $id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            $destination = $this->getParameter('kernel.project_dir').'/web/images';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME );
            $newFilename = $originalFilename.'.'.$uploadedFile->guessExtension();

            $articleOldFilename = $article->getImageFilename();
            $fs = new Filesystem();
            $fs->remove($this->get('kernel')->getRootDir().'/../web/images/'.$articleOldFilename);

            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $article->setImageFilename($newFilename);

            $em = $this->getDoctrine()->getManager();
            $em->merge($article);
            $em->flush();

            return $this->redirectToRoute('user_articles');
        }

        return $this->render('article/edit.html.twig',
            ['form' => $form->createView(), 'article' => $article]
        );
    }

    /**
     * @param Request $request
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/myArticles/delete/{id}", name="article_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function delete(Request $request, $id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $articleOldFilename = $article->getImageFilename();
            $fs = new Filesystem();
            $fs->remove($this->get('kernel')->getRootDir().'/../web/images/'.$articleOldFilename);

            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            return $this->redirectToRoute('user_articles');
        }

        return $this->render('article/delete.html.twig',
            ['form' => $form->createView(), 'article' => $article]
        );
    }

    /**
     * @Route("/article/{id}", name="article_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewArticle($id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        return $this->render("article/article.html.twig",
            ['article' => $article]
        );
    }
}