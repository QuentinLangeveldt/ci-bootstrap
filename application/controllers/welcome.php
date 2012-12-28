<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller 
{
	public function __construct()
	{
		parent:: __construct();
		$this->layout->placeholder("title", "Short Stories");

		$this->load->spark('markdown-extra/0.0.0');
		
	}

	
	public function index()
	{
		
		$data['page'] = 'home';
		$this->layout->view('pages/home', $data);
	}

	public function about()
	{
		$data['page'] = 'about';
		$this->layout->view('pages/about', $data);
	}

	public function story()
	{
		$page = $this->uri->segment(3);
		$md = file_get_contents(APPPATH . 'views/stories/' . $page . '.md');
		$data['html'] = parse_markdown_extra($md);
		$data['page'] = 'story';
		$this->layout->view('story_viewer', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */