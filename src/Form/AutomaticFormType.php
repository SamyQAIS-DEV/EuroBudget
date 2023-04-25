<?php

namespace App\Form;

use App\Form\Type\DateTimeType;
use DateTimeInterface;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AutomaticFormType extends AbstractType
{
    final public const TYPES = [
        'string' => TextType::class,
        'bool' => SwitchType::class,
        'int' => NumberType::class,
        'float' => NumberType::class,
//        Attachment::class => AttachmentType::class,
//        User::class => UserChoiceType::class,
//        Tag::class => ForumTagChoiceType::class,
        DateTimeInterface::class => DateTimeType::class,
        UploadedFile::class => FileType::class,
//        CursusCategory::class => CursusCategoryChoiceType::class,
    ];

    final public const NAMES = [
//        'content' => EditorType::class,
//        'description' => TextareaType::class,
//        'short' => TextareaType::class,
//        'mainTechnologies' => TechnologiesType::class,
//        'secondaryTechnologies' => TechnologiesType::class,
//        'chapters' => ChaptersForm::class,
//        'color' => ColorType::class,
//        'links' => TextareaType::class,
//        'requirements' => TechnologyChoiceType::class,
//        'intervenants' => IntervenantsType::class,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        $refClass = new ReflectionClass($data);
        $classProperties = $refClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($classProperties as $property) {
            $name = $property->getName();
            /** @var \ReflectionNamedType|null $type */
            $type = $property->getType();
            dd($name, $type);
            if (null === $type) {
                return;
            }
            if ('requirements' === $name) {
                $builder->add('requirements', ChoiceType::class, [
                    'multiple' => true,
                ]);
            }
            // Input spécifique au niveau
            if (array_key_exists($name, self::NAMES)) {
                $builder->add($name, self::NAMES[$name], [
                    'required' => false,
                ]);
            } elseif (array_key_exists($type->getName(), self::TYPES)) {
                $builder->add($name, self::TYPES[$type->getName()], [
                    'required' => !$type->allowsNull() && 'bool' !== $type->getName(),
                ]);
            } else {
                throw new \RuntimeException(sprintf('Impossible de trouver le champs associé au type %s dans %s::%s', $type->getName(), $data::class, $name));
            }
        }
    }
}
