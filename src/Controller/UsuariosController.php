<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;       // para obtener datos de la petición (GET, POST, PUT, DELETE, etc.)
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Usuarios;
use App\Repository\UsuariosRepository;

class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'app_usuarios')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your Usuarios controller!',
            'path' => 'src/Controller/UsuariosController.php',
        ]);
    }

    #[Route('/save_usuario/{idUsuario?}', name: 'save_usuario', methods: ['POST','PUT'])]
    public function createUsuario(Request $request, UsuariosRepository $usuariosRepository, EntityManagerInterface $em, int $idUsuario = null): JsonResponse
    {
        $A_data = json_decode($request->getContent(), true);
        $txt_user=$A_data["txt_user"];
        $txt_pass=$A_data["txt_pass"];

        if(empty($txt_user) || empty($txt_pass)){
            $response = new JsonResponse(); // convierte un array en JSON
            $response->setData(['success' => false, 'data' => [], 'msg' => 'debe incluir nombre y clave']);
            return $response;
        }

        $encryptedPass=$txt_pass;

        $now=new \DateTime();

        // Create or Update
        if($idUsuario){
            $usuario=$em->getRepository(Usuarios::class)->find($idUsuario); // update
            $msg="Usuario actualizado con éxito";
            $statusCode=Response::HTTP_OK; // 200
        } else {
            $usuario = new Usuarios();
            $usuario->setCreatedAt($now);
            $msg="Usuario Creado con éxito";
            $statusCode=Response::HTTP_CREATED; // 201
        }
        // Shared Props
        $usuario->setUsuario($txt_user);
        $usuario->setPass($encryptedPass);
        $usuario->setUpdatedAt($now);

        $em->persist($usuario); // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $em->flush(); // actually executes the queries (i.e. the INSERT query)

        return new JsonResponse([
            'success' => true,
            'data' => ["id" => $usuario->getId(), "usuario" => $usuario->getUsuario(), "fecha" => $usuario->getCreatedAt()->format('d-m-Y H:i')],
            "msg" => $msg,
            "status" => $statusCode
        ]);
       
    }

    #[Route('/list_usuarios', name: 'list_usuarios')]
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

        return new JsonResponse([
            'success' => true,
            'data' => $A_usuarios,
            'msg' => "Usuarios obtenidos con éxito",
            'cant' => count($A_usuarios),
            "status" => Response::HTTP_OK
        ]);
    }

    #[Route('/delete_usuario/{idUsuario}', name: 'delete_usuario', methods: ['DELETE'])]
    public function deleteUsuario(int $idUsuario, UsuariosRepository $usuariosRepository, EntityManagerInterface $em)
    {

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
            "status" => Response::HTTP_NO_CONTENT
        ]);

    }
}



