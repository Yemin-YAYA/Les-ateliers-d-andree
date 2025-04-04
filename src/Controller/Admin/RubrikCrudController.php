<?php

namespace App\Controller\Admin;

use App\Entity\Rubrik;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RubrikCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rubrik::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('une rubrique')
            ->setPaginatorPageSize(5);
    }
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
