<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Form\ValidationCommandeType;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */


    public function index(CommandeRepository $commandeRepository,LigneCommandeRepository $ligneCommandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
            'lignesCommandes'=>$ligneCommandeRepository->findAll()
        ]);
    }

    /**
     * @Route("/print", name="listcommandes")
     */
    public function list()
    {

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $commandes=$this->getDoctrine()->getRepository(Commande::class)->findAll();
        $lignesCommandes=$this->getDoctrine()->getRepository(LigneCommande::class)->findAll();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commande/mypdf.html.twig', [
            'title' => "Commandes",'lignesCommandes'=>$lignesCommandes
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
    /**
     * @Route("/add", name="commande_add")
     */
    public function success(SessionInterface $session, ProductsRepository $productRepository,UserRepository  $userRepository )
    {

        $panier = $session->get('panier' , []);
        $panierWithData = [];
        $total=0;
        foreach ($panier as $id => $quantity){
            $produit=$productRepository->find($id);
            $panierWithData[] = [
                'produit' => $produit,
                'quantite' => $quantity
            ];
            $total += $produit->getPrice() * $quantity;
        }
        $order=new Commande();
        //$order->setIdUser($this->getUser());
        $order->setUser($userRepository->find(1));
        $order->setMontant($total);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order);
        $this->getDoctrine()->getManager()->flush();

        foreach ($panierWithData as $item) {
            $productOrder=new LigneCommande();

            $productOrder->setQuantite($item['quantite']);
            $productOrder->setProduit($productRepository->find($item['produit']));
            $productOrder->setPrixProduit($productRepository->find($item['produit'])->getPrice()*$item['quantite']);
            $productOrder->setCommande($order);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($productOrder);
            $product=$productRepository->find($item['produit']);
            $product->setQuantite($product->getQuantite()-$item['quantite']);
            $this->getDoctrine()->getManager()->flush();

        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order);
        $this->getDoctrine()->getManager()->flush();
        $session->remove('panier');
        return $this->redirectToRoute('cart_index');
        //return $this->render('commande/new.html.twig', [
        // 'commande' => $order,
        //]);


    }


    /**
     * @Route("/registration", name="registration")
     */
    public function registrer(Request $request, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ValidationCommandeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration = $form->getData();
            $message = (new \Swift_Message('Validation Commande'))
                ->setFrom('bensalah.yass@esprit.tn')
                ->setTo($registration['email'])
                ->setBody(
                    $this->renderView(
                        'commande/valid.html.twig', compact('registration')
                    ),
                    'text/html'
                );

            // On envoie le message

            $mailer->send($message);

            $this->addFlash('message', 'Le message a bien été envoyé');
            //return $this->redirectToRoute('');
        }

        return $this->render('commande/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
