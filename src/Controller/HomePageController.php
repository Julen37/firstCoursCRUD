<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\CrudType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')] // le '/' permet de renvoyer la page par default directement a l'ouverture sinon c'etait /home/page // ne pas toucher le nom
    public function homePage(): Response // changer le index() au nom de la page pour etre precis // public veut dire qu'ele est accessible partout // response renvoi un objet/une reponse
    {
        return $this->render('home_page/homePage.html.twig', [ // renommer là où on prend la page qui est nommé index.html.twig normalement
            'controller_name' => 'HomePageController', //indique le nom du controller
        ]);
    }
    #[Route('/create', name: 'app_create_form')] 
    public function create_form(Request $request): Response // attention prendre le httpfoundation request
    {
        $crud = new Crud(); // creation d'une variable qui contient une nouvelle instanciation de la class de l'entity Crud (title et content)
        $form = $this->createForm(CrudType::class, $crud); // dans tout l'objet on utilise la methode createdform sur le formulaire de la class CrudType qui est associé a l'instance $crud
        $form->handleRequest($request); // on utilise le handlerequest sur la variable $request

        return $this->render('form/createForm.html.twig', [ 
            'form' => $form->createView() // form est la valeur qu'on utilisera en frontend
        ]);
    }
}
