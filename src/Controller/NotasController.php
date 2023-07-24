<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Notas;
use App\Entity\Usuarios;
use App\Entity\Categorias;

use App\Repository\NotasRepository;

use App\Service\TimeHelper;

class NotasController extends AbstractController
{
    private $timeHelper;

    public function __construct(TimeHelper $timeHelper)
    {
        $this->timeHelper = $timeHelper;
    }



    #[Route('/notas', name: 'app_notas')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your Notas controller!',
            'path' => 'src/Controller/NotasController.php',
        ]);
    }

    // las notas de Todos los Usuarios
    #[Route('/list_notas', name: 'list_notas')]
    public function listNotas(Request $request, NotasRepository $notasRepository)
    {
        $notas = $notasRepository->findAll();
        $A_notas = [];
        foreach ($notas as $nota){

            $categorias = $nota->getCategorias();
            $A_categorias = [];
            foreach ($categorias as $categoria) {
                $A_categorias[] = [
                    'id' => $categoria->getId(),
                    'categoria' =>$categoria->getCategoria()
                ];
            }

            $usuario = $nota->getUsuario();            

            $A_notas[] = [
                'id' => $nota->getId(),
                'notas' => $nota->getNota(),
                'info' => $nota ->getDescripcion(),
                'categorias'=> $A_categorias,
                'idUsuario' => $usuario->getId(),
                'usuario' => $usuario->getUsuario(),
                'createdAt' => $nota->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $nota->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse([
            'success' => true,
            'data' => $A_notas,
            'msg' => "Notas obtenidas con éxito",
            'cant' => count($A_notas)
        ]);

    }

    // Las notas de un Usuario
    #[Route('/list_notas/{idUsuario}', name: 'list_notas_usuario')]
    public function listNotasUsuario(int $idUsuario, Request $request, NotasRepository $notasRepository, EntityManagerInterface $em)
    {
        $usuario = $em->getRepository(Usuarios::class)->find($idUsuario);
        if(empty($usuario)){ $response = new JsonResponse(); $response->setData(['success' => false, 'data' => [], 'msg' => 'Usuario incorrecto']); return $response; }
        
        $notas = $notasRepository->findBy(['usuario' => $usuario]);
        $A_notas = [];
        foreach ($notas as $nota ){

            $categorias = $nota->getCategorias();
            $A_categorias = [];
            foreach ($categorias as $categoria) {
                $A_categorias[] = [
                    'id' => $categoria->getId(),
                    'categoria' =>$categoria->getCategoria()
                ];
            }

            $A_notas[] = [
                'id' => $nota ->getId(),
                'notas' => $nota ->getNota(),
                'info' => $nota ->getDescripcion(),
                'categorias' => $A_categorias,
                'createdAt' => $nota ->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $nota ->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        $txt_usuario=$usuario->getUsuario();

        return new JsonResponse([
            'success' => true,
            'data' => $A_notas,
            'msg' => "Notas de $txt_usuario obtenidas con éxito",
            'cant' => count($A_notas),
            'status' => Response::HTTP_OK
        ]);

    }


    // Las notas Antiguas (7 días)
    #[Route('/list_notas/{idUsuario}/antiguas', name: 'notas_antiguas', methods: ['GET'])]
    public function listaNotasAntiguas(int $idUsuario, NotasRepository $notasRepository, EntityManagerInterface $em): Response
    {
        $usuario = $em->getRepository(Usuarios::class)->find($idUsuario);
        if(empty($usuario)){ $response = new JsonResponse(); $response->setData(['success' => false, 'data' => [], 'msg' => 'Usuario incorrecto']); return $response; }
        
        $fechaDeCorte = new \DateTime();
        $fechaDeCorte->modify('-7 days'); // '-1 year', '-5 minutes', '-30 days', '-24 hours'

        // También podríamos filtrar por updatedAt así
        // las notas modificas resetean el tiempo de 7 días
        $notas = $notasRepository->createQueryBuilder('n')
            ->where('n.createdAt < :fechaDeCorte')
            ->andWhere('n.usuario = :usuario')
            ->setParameter('fechaDeCorte', $fechaDeCorte)
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getResult();

            $A_notas = [];
            foreach ($notas as $nota ){
    
                $categorias = $nota->getCategorias();
                $A_categorias = [];
                foreach ($categorias as $categoria) {
                    $A_categorias[] = [
                        'id' => $categoria->getId(),
                        'categoria' =>$categoria->getCategoria()
                    ];
                }
    
                // devuelvo la antiguedad exacta con el timeHelper
                $timeDiff = $this->timeHelper->humanReadableTimeDiff($nota ->getCreatedAt(), true);

                $A_notas[] = [
                    'id' => $nota ->getId(),
                    'notas' => $nota ->getNota(),
                    'info' => $nota ->getDescripcion(),
                    'categorias' => $A_categorias,
                    'createdAt' => $nota ->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $nota ->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'age' => $timeDiff
                ];
            }
        
            return new JsonResponse([
                'success' => true,
                'data' => $A_notas,
                'msg' => "Notas antiguas obtenidas con éxito",
                'cant' => count($A_notas),
                'status' => Response::HTTP_OK
            ]);
    }


    // Craete or Update
    #[Route('/save_nota/{idUsuario}/{idNota?}', name: 'save_nota', methods: ['POST','PUT'])]
    public function saveNota(int $idUsuario, Request $request, EntityManagerInterface $em, int $idNota = null): JsonResponse
    {
        $A_data = json_decode($request->getContent(), true);
        $txt_nota=$A_data["txt_nota"];
        $txt_info=$A_data["txt_info"];
        $A_categoriasIds = $A_data['categoriasIds']??[]; // assuming you are passing array of categoria ids

        $usuario = $em->getRepository(Usuarios::class)->find($idUsuario);

        if(empty($usuario)){ $response = new JsonResponse(); $response->setData(['success' => false, 'data' => [], 'msg' => 'Usuario incorrecto']); return $response; }
        if(empty($txt_nota) || empty($txt_info) || empty($usuario)){ $response = new JsonResponse(); $response->setData(['success' => false, 'data' => [], 'msg' => 'Datos Incompletos']); return $response; }

        $now=new \DateTime();

        // Create or Update
        // $nota = $idNota ? $em->getRepository(Notas::class)->find($idNota) : new Notas();
        if($idNota){    
            $nota=$em->getRepository(Notas::class)->find($idNota);  // update
            $msg="Nota Actualizada con éxito";
            $statusCode=Response::HTTP_OK; // 200
        } else {        
            $nota = new Notas();                                    // insert
            $nota->setCreatedAt($now);
            $nota->setUsuario($usuario);
            $msg="Nota Creada con éxito";
            $statusCode=Response::HTTP_CREATED; // 201
        }
        // Shared Props
        $nota->setNota($txt_nota);
        $nota->setDescripcion($txt_info);
        $nota->setUpdatedAt($now);

        $A_categorias=[];
        foreach($A_categoriasIds as $idCate) {
            $categoria = $em->getRepository(Categorias::class)->find($idCate);
            if($categoria){ // solo la almacena si el id existe
                $nota->addCategoria($categoria);
                
                $A_categorias[] = [
                    'id' => $categoria->getId(),
                    'categoria' =>$categoria->getCategoria()
                ];
            }
        }

        $em->persist($nota);
        $em->flush(); 

        // response 200 / 201
        return new JsonResponse([
            'success' => true,
            'data' => ["id" => $nota->getId(), "nota" => $nota->getNota(),"categorias" => $A_categorias, "fecha" => $nota->getCreatedAt()->format('d-m-Y H:i')],
            "msg" => $msg,
            "status" => $statusCode
        ]);
        
    }

    #[Route('/delete_nota/{idNota}', name: 'delete_nota', methods: ['DELETE'])]
    public function deleteNota(int $idNota, NotasRepository $notasRepository, EntityManagerInterface $em)
    {
 
        if (!$idNota) {  return new JsonResponse([ 'success' => false, 'msg' => 'No se ha proporcionado un ID de nota', 'status'=>Response::HTTP_BAD_REQUEST ]); } //400
        $notas = $notasRepository->find($idNota);
        if (!$notas) {    return new JsonResponse([ 'success' => false, 'msg' => "No se encontró la nota con el ID $idNota", 'status' => Response::HTTP_NOT_FOUND]); } //404
    
        $txt_nota = $notas->getNota(); // use the getter method to retrieve the name

        $em->remove($notas); // si fueran varios resultados, utilizo $em->remove($usuario) dentro de un foreach
        $em->flush();
        
        // response 204
        return new JsonResponse([
            'success' => true,
            'data' => $txt_nota,
            'msg' => "Nota '$txt_nota' eliminada con éxito",
            "status" => Response::HTTP_NO_CONTENT
        ]);

    }
}
