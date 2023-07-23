<?php

namespace App\Controller;

/**
 * Controller Para Testing de Rutas, no va a producción
 * 
 * Pruebas de recepción de datos por GET, POST, MultiPart, JSON
 * 
 */


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;       // para obtener datos de la petición (GET, POST, PUT, DELETE, etc.)
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UsuariosRepository;

class TestingController extends AbstractController
{
    #[Route('/test/usuarios', name: 'app_usuarios')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsuariosController.php',
        ]);
    }

    #[Route('/test/create_usuario', name: 'create_usuario', methods: ['POST'])]
    public function createUsuario(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // _GET || _POST || multiPart
        // $txt_user= $request->get('txt_user', null);
        // $txt_pass= $request->get('txt_pass', null);
        // $A_allData = $request->request->all(); // Array asociativo con todos los datos
        $A_data = json_decode($request->getContent(), true); // En caso de enviar datos en formato JSON
        $txt_user=$A_data["txt_user"];
        $txt_pass=$A_data["txt_pass"];

        if(empty($txt_user) || empty($txt_pass)){
            $response = new JsonResponse(); // convierte un array en JSON
            $response->setData(['success' => false, 'data' => [], 'msg' => 'debe incluir nombre y clave']);
            return $response;
        }

        $encryptedPass=$txt_pass;

        //$now=date('Y-m-d H:i:s');
        $now=new \DateTime();

        $usuario = new Usuarios();
        $usuario->setUsuario($txt_user);
        $usuario->setPass($encryptedPass);
        $usuario->setCreatedAt($now);
        $usuario->setUpdatedAt($now);

        $em->persist($usuario); // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $em->flush(); // actually executes the queries (i.e. the INSERT query)

        $response = new JsonResponse(); // convierte un array en JSON
        $response->setData([
            'success' => true,
            'data' => ["id" => $usuario->getId(), "usuario" => $usuario->getUsuario(), "fecha" => $usuario->getCreatedAt()->format('d-m-Y H:i')],
            "msg" => "usuario creado con éxito"
        ]);
        return $response;

        // standard Html Response
        //return new Response('Saved new user with id '.$usuario->getId());
    }

    #[Route('/test/list_usuarios', name: 'list_usuarios')]
    public function listUsuarios(Request $request, UsuariosRepository $usuariosRepository)
    {

        $usuarios = $usuariosRepository->findAll();
        $A_usuarios = [];
        foreach ($usuarios as $usuario){
            $A_usuarios[] = [
                'id' => $usuario->getId(),
                'usuario' => $usuario->getUsuario(),
                'pass' => $usuario->getPass(),
                'createdAt' => $usuario->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $usuario->getUpdatedAt()->format('Y-m-d H:i:s'),
                'deletedAt' => $usuario->getDeletedAt()
            ];
        }

        $response = new JsonResponse(); // convierte un array en JSON
        $response->setData([
            'success' => true,
            'data' => $A_usuarios,
            'msg' => "Usuarios obtenidos con éxito",
            'cant' => count($A_usuarios)
        ]);
        return $response;
    }

    #[Route('/test/delete_usuario/{idUsuario}', name: 'delete_usuario', methods: ['POST'])]
    public function deleteUsuario(int $idUsuario, UsuariosRepository $usuariosRepository, EntityManagerInterface $em)
    {
 
        //$idUsuario = $request->get('id_usuario', null);

        if (!$idUsuario) {  return new JsonResponse([ 'success' => false, 'msg' => 'No se ha proporcionado un ID de usuario' ]); }
        $usuario = $usuariosRepository->find($idUsuario); // find(), findBy(), findAll(), findOneBy() | ej: findBy(['status' => 3])
        if (!$usuario) {    return new JsonResponse([ 'success' => false, 'msg' => "No se encontró al usuario con el ID $idUsuario"]); }
    
        $nombre = $usuario->getUsuario(); // use the getter method to retrieve the name

        //foreach($usuarios as $usuario) { $em->remove($usuario)} // eliminación de múltiples registros
        $em->remove($usuario); // si fueran varios resultados, utilizo $em->remove($usuario) dentro de un foreach
        $em->flush();
        
        return new JsonResponse([
            'success' => true,
            'data' => $nombre,
            'msg' => "Usuario $nombre eliminado con éxito",
        ]);

    }

    #[Route('/test/delete_tomas', name: 'delete_tomas', methods: ['DELETE'])]
    public function deleteManyUsers(EntityManagerInterface $em)
    {

        // Create a QueryBuilder instance
        $queryBuilder = $em->createQueryBuilder();

        // Construct the query
        $query = $queryBuilder->delete(Usuarios::class, 'u')
            ->where('u.usuario = :usuario')
            ->setParameter('usuario', 'Tomas')
            ->getQuery();

        // Execute the query and get the number of affected rows
        $numDeleted = $query->execute();

        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'msg' => "Number of users deleted: $numDeleted",
        ]);

        return $response;
    }

    #[Route('/test/soft_delete', name: 'soft_delete')]
    public function softDeleteUsuarios(EntityManagerInterface $em)
    {
        // Create a QueryBuilder instance
        $queryBuilder = $em->createQueryBuilder();
        //$now=date('Y-m-d H:i:s');
        $now=new \DateTime();

        // Construct the query
        $query = $queryBuilder->update(Usuarios::class, 'u')
            ->set('u.deletedAt', ':now')
            ->where('u.usuario = :usuario')
            ->setParameters([
                'usuario' => 'Tomas',
                'now' => $now,
            ])
            ->getQuery();

        // Execute the query and get the number of affected rows
        $numUpdated = $query->execute();

        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'msg' => "Number of users updated: $numUpdated",
        ]);

        return $response;
    }


    // Catch ALL !!! (este controller y ruta debe ir al final de todos!)
    #[Route('/{req}', name: 'catch_all', requirements: ['req' => '.*'], methods: ['GET', 'POST', 'PUT', 'DELETE'])]
    public function catchAll($req): Response
    {
        // You can handle the request here...
        return new Response("Ruta de Catch-All [TestingController.php]");
    }
}
