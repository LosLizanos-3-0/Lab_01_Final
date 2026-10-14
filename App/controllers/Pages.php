<?php 
class Pages extends Controller {
    
    // Declaramos la propiedad aquí para evitar el aviso en PHP 8.2+
    public $userModel;

    public function __construct(){
        $this->userModel = $this->model('User');
    }
        
    public function index(){
        $user = $this->userModel->getUsers(); 
        
        $data = ['title' => 'Página principal',
                 'name'  => 'Jorge Ruiz',
                 'users' => $user];
        
        $this->view('pages/index', $data);
    }

    public function about(){            
        $this->view('pages/about');
    }
}
?>