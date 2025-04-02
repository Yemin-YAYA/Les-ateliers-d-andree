<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->onlyOnIndex(),
            TextField::new('title', 'Titres')->setColumns('col-md-4'),
            AssociationField::new('rubrik', "Rubriques")->setColumns('col-md-4'),
            TextField::new('content', 'Déscriptif de l\'image 1')->setColumns('col-md-6'),
            
            ImageField::new('image1')
            ->setUploadDir('public/divers/images')
            ->setBasePath('divers/images')
            ->setFormTypeOption('required', false)
                ->setColumns('col-md-2'),

            ImageField::new('image2')
            ->setUploadDir('public/divers/images')
                ->setBasePath('divers/images')
                ->setSortable(false)
                ->setFormTypeOption('required', false)
                ->setColumns('col-md-2'),

            ImageField::new('image3')
                ->setUploadDir('public/divers/images')
                ->setBasePath('divers/images')
                ->setSortable(false)
                ->setFormTypeOption('required', false)
                ->setColumns('col-md-2'),

                TextField::new('content2', "Déscriptif de l'image 2")->setColumns('col-md-6'),
                
            ImageField::new('image4')
            ->setUploadDir('public/divers/images')
                ->setBasePath('divers/images')
                ->setSortable(false)
                ->setFormTypeOption('required', false)
                ->setColumns('col-md-2'),

            ImageField::new('image5')
                ->setUploadDir('public/divers/images')
                ->setBasePath('divers/images')
                ->setSortable(false)
                ->setFormTypeOption('required', false)
                ->setColumns('col-md-2'),

                ImageField::new('image6')
                ->setUploadDir('public/divers/images')
                ->setBasePath('divers/images')
                ->setSortable(false)
                ->setFormTypeOption('required', false)
                ->setColumns('col-md-2'),

                AssociationField::new('user', 'Utilisateurs')->setColumns('col-md-4'),
                DateField::new('createdAt','Date de création'),
                BooleanField::new('isPublished')            
                ->setColumns('col-md-1')
                ->setLabel('Publié'),
            ];
    
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un article')
            ->setPageTitle('index', 'Liste des articles') // Mise à jour du titre de la page index
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(7);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('user')
            ->add('title')
            ->add('rubrik')
            ->add('createdAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        $returnToList = Action::new('returnToList', '↩ Retour à la liste')
            ->linkToUrl(function () {
                return $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('index')
                    ->generateUrl();
            })
            ->setCssClass('btn btn-secondary');

        return $actions
            ->setPermission(Action::DELETE, 'ROLE_ADMIN') // Restriction d'accès pour la suppression
            ->disable(Action::SAVE_AND_CONTINUE) // Supprime "Sauvegarder et modifier"
            ->add(Crud::PAGE_EDIT, $returnToList); // Ajoute le bouton sur la page d'édition
    }
}
