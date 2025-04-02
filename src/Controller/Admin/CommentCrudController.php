<?php
namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; 
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

#[IsGranted('ROLE_MODO')]
class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
        IntegerField::new('id')->onlyOnIndex(),       
        TextField::new('post.title', 'Titre du Post')->onlyOnIndex(),
        TextField::new('post.rubrik.name', 'Nom de la rubrique')->onlyOnIndex(),
        TextareaField::new('content', 'Commentaires')->setColumns('col-md-6'),
        DateField::new('createdAt')->onlyOnIndex(),
        AssociationField::new('user', 'Pseudo')->setColumns('col-md-4'),
    ];
    }
    public function configureFilters(Filters $filters): Filters
    {
    return $filters
        ->add('createdAt')
        ->add('user');     
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud            
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(5);
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW) // Désactiver la création
            ->disable(Action::EDIT) // Désactiver l'édition/modification
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')  
            ->setPermission(Action::DELETE, 'ROLE_MODO');  
    }
}
