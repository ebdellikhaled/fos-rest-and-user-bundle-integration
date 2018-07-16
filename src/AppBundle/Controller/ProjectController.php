<?php
/**
 * Created by PhpStorm.
 * User: digitus
 * Date: 12/9/17
 * Time: 10:40 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectSkills;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSubscriptions;
use AppBundle\Form\Type\ProjectType;
use AppBundle\Form\Type\TaskType;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class ProjectController
 * @package AppBundle\Controller
 * @RouteResource("Users", pluralize=false)
 */
class ProjectController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Project
     *
     * @Annotations\Get("/projects/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
    }

    /**
     * @Annotations\Get("/projects/me")
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function userProjectsAction(Request $request)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->findBy(['user' => $this->getUser()->getId()], ['created' => 'desc']);
        return new View($project, Response::HTTP_OK);

    }

    /**
     * @Annotations\Get("/projects")
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function allProjectsAction(Request $request)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->findBy( [], ['created' => 'desc']);
        return new View($project, Response::HTTP_OK);

    }

    /**
     * @Annotations\Post("/projects")
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a new Project has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'csrf_protection' => false,
        ]);
        $skills = $request->request->get('skills');
        $subscriber = new UserSubscriptions(0, $this->getUser(), $project);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $project Project
         */
        $project = $form->getData();

        $file = $request->files->get('brochure');

        $fileName = $this->get('app.brochure_uploader')->upload($file);

        $project->setBrochure($fileName);
        $project->addContributor($subscriber);
        $em = $this->getDoctrine()->getManager();
        $project->setUser($this->getUser());
        $em->persist($project);
        foreach ($skills as $key => $value) {
            $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneBy(["title"=> $value['text']]);
            $projectSkill = new ProjectSkills(0, $project, $skill);
            $em->persist($projectSkill);
        }
        $em->flush();
        return new View($form->getData(), Response::HTTP_CREATED);
    }

    /**
     * @Annotations\Post("/projects/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         204 = "Returned when an existing Project has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function putAction(Request $request, int $id)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'csrf_protection' => false,
        ]);
       /* $skills = $request->request->get('skills');*/
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $project Project
         */
        $project = $form->getData();

        $file = $request->files->get('brochure');
        $projectUpdated = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        if ($file){
            $fileName = $this->get('app.brochure_uploader')->upload($file);
            $project->setBrochure($fileName);
            if ($projectUpdated->getBrochure() !== $fileName)
                $projectUpdated->setBrochure($fileName);
        }
        $projectUpdated->setTitle($project->getTitle());
        $projectUpdated->setDescription($project->getDescription());
        $em = $this->getDoctrine()->getManager();
        $em->persist($projectUpdated);
       /* foreach ($skills as $key => $value) {
            $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneBy(["title"=> $value['text']]);
            $projectSkill = new ProjectSkills(0, $projectUpdated, $skill);
            $em->persist($projectSkill);
        }*/
        $em->flush();

        return new View('project updated', Response::HTTP_CREATED);
    }


    /**
     * @Annotations\Delete("/projects/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     statusCodes={
     *         204 = "Returned when an existing Project has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function deleteAction(int $id)
    {
        /**
         * @var $project Project
         */
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        if ($project === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }
        if ($project->getUser()->getId() !== $this->getUser()->getId() && $this->getUser()->getRole() !== 'ROLE_SUPER_ADMIN') {
            return new View($this->getUser()->getRole() . ' it is not your own project', Response::HTTP_UNAUTHORIZED);
        }

        $em = $this->getDoctrine()->getManager();
        if ($project->getUser()->getId() == $this->getUser()->getId() || $this->getUser()->getRole() == 'ROLE_SUPER_ADMIN') {
            $em->remove($project);
            $em->flush();
        }
        return new View(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @Annotations\Post("/projects/subscribe/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a new Project has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function addContribAction(Request $request, $id)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        $subscription = new UserSubscriptions(0, $this->getUser(), $project);
        $em = $this->getDoctrine()->getManager();
        $project->addContributor($subscription);
        $em->persist($project);
        $em->flush();
        return new View('subcription added', Response::HTTP_CREATED);
    }

    /**
     * @Annotations\Post("/projects/subscribe/{id}/{userEmail}")
     * @param int $id, string $userEmail
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a new Contributor added",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function invContribAction(Request $request, $id, $userEmail)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['email' => $userEmail]);
        $subscription = new UserSubscriptions(0, $user, $project);
        $em = $this->getDoctrine()->getManager();
        if ($user) {
            $project->addContributor($subscription);
            $em->persist($subscription);
            $em->persist($project);
            $em->flush();
            return new View('subcription added', Response::HTTP_CREATED);
        }
        return new View('user not found', Response::HTTP_NOT_MODIFIED);
    }

    /**
     * @Annotations\Get("/projects/subscribers/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a new Project has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function GetContribAction(Request $request, $id)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        $contributors = $project->getContributors();
        return new View($contributors, Response::HTTP_OK);
    }

    /**
     * @Annotations\Delete("/projects/subscribe/{id}/{idUser}")
     * @param int $id, int $idUser
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a contributor has been deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function delContribAction(Request $request, $id, $idUser)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($idUser);
        $em = $this->getDoctrine()->getManager();
        $project->removeContributors($user);
        $em->persist($project);
        $em->flush();
        return new View('contributor deleted', Response::HTTP_OK);
    }

    /**
     * @Annotations\Post("/projects/approve/{id}/{idUser}")
     * @param int $id, int $idUser
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\ProjectType",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a contributor has been approved",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function approveContribAction(Request $request, $id, $idUser)
    {
        $subscription = $this->getDoctrine()->getRepository('AppBundle:UserSubscriptions')->findOneBy(['user'=> $idUser, 'project' => $id]);
        $em = $this->getDoctrine()->getManager();
        $subscription->setEnabled(!$subscription->getEnabled());
        $em->persist($subscription);
        $em->flush();
        return new View('contributor toggle approve', Response::HTTP_OK);
    }

    /**
     * @Annotations\Get("/projects/skills/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     */
    public function getProjectSkillsAction($id)
    {
        return $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id)->getSkills();
    }


    /**
     * @Annotations\Post("/projects/{id}/task")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         201 = "Returned when a new Task has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function addTaskAction(Request $request, int $id)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->request->get('userId'));
        $task = new Task();
        $task->setStatus(false);
        $task->setProject($project);
        $task->setUser($user);
        $task->setDescription($request->request->get('description'));
        $task->setTitle($request->request->get('title'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();
        return new View(true, Response::HTTP_CREATED);
    }



    /**
     * @Annotations\Post("/tasks/{id}/close")
     * @param int $id
     * @return \AppBundle\Entity\Project|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         201 = "Returned when a new Task has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function closeTaskAction(Request $request, int $id)
    {
        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);

        $file = $request->files->get('document');
        if ($file) {
            $fileName = $this->get('app.doc_tasks_uploader')->upload($file);
            $task->setDocument($fileName);
            $task->setStatus(true);
            $task->setNote($request->request->get('title'));
            $task->setUpdated(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return new View(true, Response::HTTP_OK);
        }

        return new View(false, Response::HTTP_NOT_MODIFIED);
    }


    /**
     * @Annotations\Get("/projects/{id}/tasks")
     * @param int $id
     * @return \AppBundle\Entity\Task|View|object
     */
    public function listTasksAction(Request $request, $id)
    {
        $tasks = $this->getDoctrine()->getRepository('AppBundle:Task')->findBy(['project'=>$id]);
        return new View($tasks, Response::HTTP_OK);
    }

    /**
     * @Annotations\Get("/task/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Task|View|object
     */
    public function getTaskAction(Request $request, $id)
    {
        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);
        return new View($task, Response::HTTP_OK);
    }
}