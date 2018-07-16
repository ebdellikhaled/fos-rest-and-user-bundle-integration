<?php
/**
 * Created by PhpStorm.
 * User: digitus
 * Date: 12/9/17
 * Time: 10:40 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Cours;
use AppBundle\Entity\CoursSkills;
use AppBundle\Entity\User;
use AppBundle\Entity\UserCours;
use AppBundle\Form\Type\CoursType;
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
 * Class CoursController
 * @package AppBundle\Controller
 * @RouteResource("Cours", pluralize=false)
 */
class CoursController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Cours
     *
     * @Annotations\Get("/cour/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
    }

    /**
     * @Annotations\Get("/cours/me")
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function userCoursAction(Request $request)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(['user' => $this->getUser()->getId()], ['created' => 'desc']);
        return new View($cours, Response::HTTP_OK);

    }

    /**
     * @Annotations\Get("/cours")
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function allCoursAction(Request $request)
    {
        $courses = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy( [], ['created' => 'desc']);
        return new View($courses, Response::HTTP_OK);

    }

    /**
     * @Annotations\Post("/cours")
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a new Cours has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours, [
            'csrf_protection' => false,
        ]);
        $skills = $request->request->get('skills');
        $participant = new UserCours(1,0, $this->getUser(), $cours);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $cours Cours
         */
        $cours = $form->getData();

        $file = $request->files->get('brochure');

        $fileName = $this->get('app.brochure_uploader')->upload($file);

        $cours->setBrochure($fileName);
        $cours->addParticipant($participant);
        $em = $this->getDoctrine()->getManager();
        $cours->setUser($this->getUser());
        $em->persist($cours);
        foreach ($skills as $key => $value) {
            $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneBy(["title"=> $value['text']]);
            $coursSkill = new CoursSkills(0, $cours, $skill);
            $em->persist($coursSkill);
        }
        $em->flush();
        return new View($form->getData(), Response::HTTP_CREATED);
    }

    /**
     * @Annotations\Post("/cours/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         204 = "Returned when an existing Cours has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function putAction(Request $request, int $id)
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours, [
            'csrf_protection' => false,
        ]);
        // $skills = $request->request->get('skills');
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $cours Cours
         */
        $cours = $form->getData();

        $file = $request->files->get('brochure');
        $coursUpdated = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        if ($file){
            $fileName = $this->get('app.brochure_uploader')->upload($file);
            $cours->setBrochure($fileName);
            if ($coursUpdated->getBrochure() !== $fileName)
                $coursUpdated->setBrochure($fileName);
        }


        $coursUpdated->setTitle($cours->getTitle());
        $coursUpdated->setDescription($cours->getDescription());

        $em = $this->getDoctrine()->getManager();
        $em->persist($coursUpdated);
        /*foreach ($skills as $key => $value) {
            $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneBy(["title"=> $value['text']]);
            $coursSkill = new CoursSkills(0, $coursUpdated, $skill);
            $em->persist($coursSkill);
        }*/
        $em->flush();

        return new View('cours updated', Response::HTTP_CREATED);
    }


    /**
     * @Annotations\Delete("/cours/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     statusCodes={
     *         204 = "Returned when an existing Cours has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function deleteAction(int $id)
    {
        /**
         * @var $cours Cours
         */
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        if ($cours === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }
        if ($cours->getUser()->getId() !== $this->getUser()->getId() && $this->getUser()->getRole() !== 'ROLE_SUPER_ADMIN') {
            return new View('it is not your own cours', Response::HTTP_UNAUTHORIZED);
        }

        $em = $this->getDoctrine()->getManager();
        if ($cours->getUser()->getId() == $this->getUser()->getId() || $this->getUser()->getRole() == 'ROLE_SUPER_ADMIN') {
            $em->remove($cours);
            $em->flush();
        }
        return new View(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @Annotations\Post("/cours/subscribe/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a new Cours has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function addParticipantAction(Request $request, $id)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $subscription = new UserCours(0,0, $this->getUser(), $cours);
        $em = $this->getDoctrine()->getManager();
        $cours->addParticipant($subscription);
        $em->persist($cours);
        $em->flush();
        return new View('subcription added', Response::HTTP_CREATED);
    }

    /**
     * @Annotations\Post("/cours/subscribe/{id}/{userEmail}")
     * @param int $id, string $userEmail
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a new Contributor added",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function invParticipantAction(Request $request, $id, $userEmail)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['email' => $userEmail]);
        $subscription = new UserCours(0,0, $user, $cours);
        $em = $this->getDoctrine()->getManager();
        if ($user) {
            $cours->addParticipant($subscription);
            $em->persist($cours);
            $em->flush();
            return new View('subcription added', Response::HTTP_CREATED);
        }
        return new View('user not found', Response::HTTP_NOT_MODIFIED);
    }

    /**
     * @Annotations\Get("/cours/subscribers/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a new Cours has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function GetParticipantsAction(Request $request, $id)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $participants = $cours->getParticipants();
        return new View($participants, Response::HTTP_OK);
    }

    /**
     * @Annotations\Delete("/cours/subscribe/{id}/{idUser}")
     * @param int $id, int $idUser
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a contributor has been deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function delContribAction(Request $request, $id, $idUser)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($idUser);
        $em = $this->getDoctrine()->getManager();
        $cours->removeParticipants($user);
        $em->persist($cours);
        $em->flush();
        return new View('participant deleted', Response::HTTP_OK);
    }

    /**
     * @Annotations\Post("/cours/approve/{id}/{idUser}")
     * @param int $id, int $idUser
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a participant has been approved",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function approveParticipantAction(Request $request, $id, $idUser)
    {
        $subscription = $this->getDoctrine()->getRepository('AppBundle:UserCours')->findOneBy(['user'=> $idUser, 'cours' => $id]);
        $em = $this->getDoctrine()->getManager();
        $subscription->setEnabled(!$subscription->getEnabled());
        $em->persist($subscription);
        $em->flush();
        return new View('participant toggle approve', Response::HTTP_OK);
    }

    /**
     * @Annotations\Post("/cours/tuteur/{id}/{userEmail}")
     * @param int $id, string $userEmail
     * @return \AppBundle\Entity\Cours|View|object
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\CoursType",
     *     output="AppBundle\Entity\Cours",
     *     statusCodes={
     *         201 = "Returned when a new Contributor added",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function invTuteurAction(Request $request, $id, $userEmail)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['email' => $userEmail]);
        $subscription = new UserCours(0,1, $user, $cours);
        $em = $this->getDoctrine()->getManager();
        if ($user) {
            $cours->addParticipant($subscription);
            $em->persist($cours);
            $em->flush();
            return new View('subcription added', Response::HTTP_CREATED);
        }
        return new View('user not found', Response::HTTP_NOT_MODIFIED);
    }

    /**
     * @Annotations\Get("/cours/skills/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Cours|View|object
     */
    public function getCoursSkillsAction($id)
    {
        return $project = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id)->getSkills();
    }
}