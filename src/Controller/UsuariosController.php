<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;       // para obtener datos de la petición (GET, POST, PUT, DELETE, etc.)
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UsuariosRepository;

class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'app_usuarios')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsuariosController.php',
        ]);
    }

    #[Route('/create_usuario', name: 'create_usuario', methods: ['POST'])]
    public function createUsuario(Request $request, EntityManagerInterface $em): JsonResponse
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

        $response = new JsonResponse(); // convierte un array en JSON
        $response->setData([
            'success' => true,
            'data' => $A_usuarios,
            'msg' => "Usuarios obtenidos con éxito",
            'cant' => count($A_usuarios)
        ]);
        return $response;
    }
}
