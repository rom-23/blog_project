<?php

namespace App\Controller\Admin;

use App\Entity\Development\Development;
use App\Form\Development\DevelopmentFileType;
use App\Form\Modelism\ImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DevelopmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Development::class;
    }

    public function configureFields(string $pageName): iterable
    {
//        $doc     = ImageField::new('filename')->setBasePath('/uploads/devFiles')->setLabel('Document');
//        $docFile = TextareaField::new('file')->setFormType(VichImageType::class)->setLabel('Document');
        $fields = [
            IdField::new('id', 'ID')->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('content')->setFormType(CKEditorType::class),
            TextField::new('slug'),
//            CollectionField::new('files')
//                           ->setEntryType(DevelopmentFileType::class)
//                           ->setFormTypeOption('by_reference', false)
//                           ->setFormTypeOption('mapped', false)
//                           ->onlyOnForms(),
            CollectionField::new('files')->onlyOnDetail(),
            AssociationField::new('files'),
            AssociationField::new('section'),
            AssociationField::new('tags'),
            AssociationField::new('notes'),
            AssociationField::new('posts'),
            DateField::new('createdAt'),
            DateField::new('updatedAt')

        ];
//        if ($pageName === Crud::PAGE_INDEX || $pageName === Crud::PAGE_DETAIL) {
//            $fields[]= $doc;
//        } else {
//            $fields[]= $docFile;
//        }
        return $fields;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(CRUD::PAGE_INDEX, 'detail');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'All Developments')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }
}
