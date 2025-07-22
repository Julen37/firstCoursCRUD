<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\CrudType;
use App\Repository\CrudRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')] // le '/' permet de renvoyer la page par default directement a l'ouverture sinon c'etait /home/page // ne pas toucher le nom
    public function homePage(CrudRepository $crudRepo): Response // changer le index() au nom de la page pour etre precis // public veut dire qu'ele est accessible partout // response renvoi un objet/une reponse
    {
        $datas = $crudRepo->findAll(); // a acces a tous ce qu'il y a dans le repo du crud
        
        //autre possibilité mais plus long et au lieu de crud repo dans la fctn on met entitymanagerinterface
        // $datas = $entityManager
        //     ->getRepository(Crud::class) // on récupère tout
        //     ->findAll(); // on les trouvent toutes

        return $this->render('home_page/homePage.html.twig', [ // renommer là où on prend la page qui est nommé index.html.twig normalement
            'controller_name' => 'HomePageController', //indique le nom du controller
            'datas'=> $datas, // variable datas qu'on va utiliser en front avec la valeur $datas
        ]);
    }

    // route de l'envoi du formulaire --------------- CREATE
    #[Route('/create', name: 'app_create_form')] 
    public function create_form(Request $request, EntityManagerInterface $entityManager): Response // attention prendre le httpfoundation request //entityManagerinterface maintenant au lieu du getdoctrine et getmanager
    {
        $crud = new Crud(); // initialisation d'une variable qui contient une nouvelle instanciation de la class de l'entity Crud (title et content)
        $form = $this->createForm(CrudType::class, $crud); // dans tout l'objet on utilise la methode createdform sur le formulaire de la class CrudType qui est associé a l'instance $crud
        $form->handleRequest($request); // on utilise le handlerequest sur la variable $request pour les requette http 
        if  ( $form->isSubmitted() && $form->isValid()){ // si mon formulaire est submit et valid
            $entityManager->persist($crud); // alors persist prend/récupère les données
            $entityManager->flush(); // et flush envoie les données en bdd

            $this->addFlash('notice', 'Soumission réussie !');

            return $this->redirectToRoute('app_home_page'); // me redirige quand c'est fini a la page d'accueil
        }

        return $this->render('form/createForm.html.twig', [ 
            'form' => $form->createView() // form est la valeur qu'on utilisera en frontend
        ]);
    }

    // route de la modification du data qui s'affiche dans le homepage en prenant son id --------------- UPDATE
    #[Route('/update/{id}', name: 'app_home_page_updateData')] // wildcard/joker {id}
    public function update_form($id, Request $request, EntityManagerInterface $entityManager): Response //rappeler l'id / EntityManagerInterface = Doctrine = ORM Object Relation Mapping
    {
        $crud = $entityManager->getRepository(Crud::class)->find($id);
        $form = $this->createForm(CrudType::class, $crud); 
        $form->handleRequest($request);
        if  ( $form->isSubmitted() && $form->isValid()){ 
            $entityManager->persist($crud);
            $entityManager->flush(); 

            $this->addFlash('notice', 'Modification réussie !');

            return $this->redirectToRoute('app_home_page'); 
        }

        return $this->render('form/updateForm.html.twig', [ 
            'form' => $form->createView()
        ]);
    }

    // route de la suppression du data qui s'affiche dans le homepage --------------- DELETE
    #[Route('/delete/{id}', name: 'app_home_page_deleteData')] 
    public function delete_form($id, EntityManagerInterface $entityManager): Response 
    {
        $crud = $entityManager->getRepository(Crud::class)->find($id);
        $entityManager->remove($crud); 
        $entityManager->flush(); 

        $this->addFlash('notice', 'Suppression réussie !');

        return $this->redirectToRoute('app_home_page'); 
    }

}
