CodeIgniter and Twitter Bootstrap work well together. Especially if we add a layout library from the CodeIgniter Sparks repository. I'll cover creating a simple web app using Twitter bootstrap and CodeIgniter Sparks.

<!--more-->

### Set Up CodeIgniter
Create a new project and drop in a [fresh download](http://ellislab.com/codeigniter) of CodeIgniter. I created a new virtual host called `stories.dev` because I got the idea for this app while thinking about how I could present short stories on the web. You can call your whatever you like obviously - just so long as you have a virtual host ready to go.

### Configure the App
You should set the `$config['base_url']` property to `http://stories.dev` or whatever local domain you are using. I also chose to autoload a couple of helpers: `$autoload['helper'] = array('url', 'file');`. Notice that I haven't configured a database - this app doesn't need one.

### Install Sparks
 As I said, [CodeIgniter Sparks](http://getsparks.org/) is a repository of libraries and components that makes it easy to add new features to your web app.
 
 To install it, from Terminal make sure you are in your project directory, and then do:
 
     php -r "$(curl -fsSL http://getsparks.org/go-sparks)"

In terminal you should get:

    Extracting zip package ...
    Cleaning up ...
    Spark Manager has been installed successfully!

If you run into problems, there are more tips and options [available here.](http://getsparks.org/install).

### Adding Bootstrap
You can download [Bootstrap from here](http://twitter.github.com/bootstrap/index.html). Then, in the root of your project folder, create a folder called `assets`. Inside that, create folders called 'styles', 'js', and 'images'.

In the Bootstrap download, you'll find a couple of CSS files, and a Javascript file. There will also be some icons. You should copy those files into their respective places in your assets folder.

### Creating a Layout Library
We'll create a layout library... In fact, the codeIgniter forums have a good one. You can [get it here](http://codeigniter.com/wiki/layout_library/). The file should be added to `application/library`.

Set the default layout name in `Layout.php` to something like:

    public function __construct($layout = "layouts/default_layout")

You can see from the above snippet that you need to create a folder called `layouts` in `application/views`. Then create a file called `default_layout.php` and save it in your layout folder. We can use the [Bootstrap starter template](http://twitter.github.com/bootstrap/examples/starter-template.html) for our layout. You'll need to make sure that you replace the paths to the CSS with your local ones.

Remove and links to Javascript files for now. We'll be dealing with those later. One important PHP snippet to add is this:

    <?php echo $content_for_layout ?>

This will mark where your actual content will appear.

### Using the Layout
Now that we have a layout library with `<?php echo $content_for_layout ?>` ready to recieve content. We can also use placeholders to pass in a page title. In `default_layout.php` add this for the page title in the head:

    <?= $this->layout->placeholder("title"); ?>

Then in the constructor method of the `welcome` controller you can set the page title:

    $this->layout->placeholder("title", "Short Stories");

To load the content for the view you _nearly_ just do a normal `$this->load->view` . What you need to do is: `$this->layout->view('story_viewer', $data);`. That'll pass the content of the view to your layout.

### Navigation
We'll create a helper to that can utilise the `active` class for the navigational links. The helper looks like this:

    <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function active($name, $number)
    {
	    $ci = get_instance();

	    if($name == 'home' && $ci->uri->segment(2) == "" && $number == 1)
	    {
		    return "class='active'";
	    }
	    elseif($name=="about" && $ci->uri->segment(2) == "about" && $number == 2)
	    {
		    return "class='active'";
	    }
	    elseif($name=="story" && $number == 3)
	    {
		    return "class='active'";
	    }
    }

Then in the nav section of our layout, we do this:

    <ul class="nav">
        <li <?=active($page,1)?>><a href="/">Home</a></li>
        <li <?=active($page,2)?>><a href="<?=base_url()?>welcome/about">About</a></li>
        <li <?=active($page,3)?>><a href="<?=base_url()?>welcome/stories">Short Stories</a></li>
        <li <?=active($page,4)?>><a href="<?=base_url()?>welcome/contact">Contact</a></li>
    </ul>

You'll notice a variable called `$page` being called. That comes from our controller method:

    //application/controllers/welcome.php
    public function index()
	{
		$data['page'] = 'home';
		$this->layout->view('pages/home', $data);
	}

Obviously we change the `$data['page']` value differently for each controller method, so that we only activate the `active` class for the page we are viewing.

### Installing and Using a CodeIgniter Spark
Next, we'll install a Markdown Spark so that we can write our actual stories/articles using Markdown. The Spark will render them as HTML. [Here is the one](http://getsparks.org/packages/markdown-extra/versions/HEAD/show) I used. To install it, from Terminal make sure you are in your project folder, and then do:

    php tools/spark install -v0.0.0 markdown-extra

To actually use the Spark, we load it in our constructor method:

    $this->load->spark('markdown-extra/0.0.0');

Then, in the controller method we want to use to parse our Markdown files, we do this:

    public function story()
	{
		$page = $this->uri->segment(3);
		$md = file_get_contents(APPPATH . 'views/stories/' . $page . '.md');
		$data['html'] = parse_markdown_extra($md);
		$data['page'] = 'story';
		$this->layout->view('story_viewer', $data);
	}

Here, you can see that I'm collecting the page name from the URI segment. Then I'm using the PHP `file_get-contents` function to load the Markdown file I have stored in `application/views/stories`. Then, I'm using the Spark to parse the Markdown into HTML:


    $data['html'] = parse_markdown_extra($md);

 And pass it to a view called `story_viewer` (in applications/views). The `story_viewer` view just needs this: `<?=$html?>` which simply echos out the contents of the parsed file.

 The `story` method can handle any Markdown file called from `application/views/stories`.

### More Design Considerations
 I added a floating menu of the left of the page, something that is easy to do with Boostrap:

     <div class="container">
      <div class="row">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Sidebar</li>
              <li class="active"><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
            </ul>
          </div><!--/.well -->

        </div>
        <div class="span9 content"><?php echo $content_for_layout ?></div>
      </div>
      <hr />
    </div> 

### Finally...
So we have Twitter Bootstrap working with CodeIgniter 2.1.3. You've also seen how CodeIgniter Sparks work. This is a bare bones app, but at least you can see how something more substantial might be put together.



    







