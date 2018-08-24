<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class TaskController extends AbstractController
{
    /**
 * @Route("/", name="task")
 */
    public function index()
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->findAll();

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController', 'task' => $task
        ]);
    }

    /**
     * @Route("/task/create", name="task_create")
     */
    public function createToDo(Request $request)
    {
        $task = new Task;
        $form = $this->createFormBuilder($task)
            ->add('name',TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('deadline',DateTimeType::class, ['attr' => ['class' => 'formcontrol', 'style' => 'margin-bottom:15px']])
            ->add('priority',IntegerType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('submit',SubmitType::class, ['label' => 'Create task', 'attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom:15px']])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $deadline = $form['deadline']->getData();
            $priority = $form['priority']->getData();
            $now = new\DateTime('now');

            $task->setName($name);
            $task->setDescription($description);
            $task->setDeadline($deadline);
            $task->setPriority($priority);
            $task->setCreateDate($now);
            $task->setIsCompleted(false);

            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            $this->addFlash('notice', 'Task added');

            return $this->redirectToRoute('task');
        }

        return $this->render('task/create.html.twig', [
            'controller_name' => 'TaskController', 'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/task/edit/{id}", name="task_edit")
     */
    public function editToDo(Request $request, $id)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $task->setName($task->getName());
        $task->setDescription($task->getDescription());
        $task->setDeadline($task->getDeadline());
        $task->setPriority($task->getPriority());
        $task->setCreateDate($task->getCreateDate());


        $form = $this->createFormBuilder($task)
            ->add('name',TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('deadline',DateTimeType::class, ['attr' => ['class' => 'formcontrol', 'style' => 'margin-bottom:15px']])
            ->add('priority',IntegerType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('submit',SubmitType::class, ['label' => 'Edit task', 'attr' => ['class' => 'btn btn-primary', 'style' => 'margin-bottom:15px']])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $deadline = $form['deadline']->getData();
            $priority = $form['priority']->getData();
            $now = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $task = $em->getRepository(Task::class)->find($id);

            $task->setName($name);
            $task->setDescription($description);
            $task->setDeadline($deadline);
            $task->setPriority($priority);
            $task->setCreateDate($now);

            $em->flush();

            $this->addFlash('notice', 'Task edited');

            return $this->redirectToRoute('task');
        }

        return $this->render('task/edit.html.twig', [
            'controller_name' => 'TaskController', 'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/task/details/{id}", name="task_details")
     */
    public function showDetails($id)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        return $this->render('task/details.html.twig', [
            'controller_name' => 'TaskController', 'task' => $task
        ]);
    }

    /**
     * @Route("/task/delete/{id}", name="task_delete")
     */
    public function deleteTask($id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        $em->remove($task);
        $em->flush();
        $this->addFlash('notice', 'Task deleted');

        return $this->redirectToRoute('task');
    }

    /**
     * @Route("/task/complete/{id}", name="task_complete")
     */
    public function completeTask($id)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);
        if ($task->getIsCompleted()){
            $task->setIsCompleted(false);
        } else {
            $task->setIsCompleted(true);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('task');
    }
}
