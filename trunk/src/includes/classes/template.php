<?php
/**
* qTemplate Template Class
*
* Die Template Klasse von qGuestbook.
* Stammt von phpBB2, mit kleineren Anpassungen für qGuestbook.
*
* <code>
* <?php
*
* require_once 'template.php';
* $template = new qTemplate('styles/');
* $template->set_filenames(array(
*   'body' => 'index_body.html',
* ));
* $template->pparse('body');
*
* ?>
* </code>
*
* PHP versions 4 and 5
*
* LICENSE:
* Copyright (C) 2007-2008 Simon Lauger
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @category   Guestbook
* @package    qGuestbook
* @subpackage common
* @author     The phpBB Group <support@phpbb.com>
* @author     Simon Lauger <admin@simlau.net>
* @copyright  (C) 2001 The phpBB Group
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    0.2.4
* @link       http://www.simlau.net/
* @version    CVS: $Id$
* @link       http://www.simlau.net/
* @since      Class available since Release 0.2.1
*/

if (!defined('GUESTBOOK')) {
    die('Hacking attempt');
    exit;
}

Class qTemplate {
	/**
	 * Enthält den Klassennamen.
	 *
	 * @var    string
	 * @access private
	 */
	private $classname = 'qTemplate';

	/**
	 * Enthält alle Template Daten als Multi Dimensionales Array.
	 *
	 * @var    array
	 * @access private
	 */
	private $_tpldata = array();

	/**
	 * Hashes der Dateinamen für jedes Template.
	 *
	 * @var    array
	 * @access private
	 */
	private $files = array();

	/**
	 * Enthält den Wurzeldpfad.
	 *
	 * @var    string
	 * @access private
	 */
	private $root = '';

	/**
	 * Enthält den kompilierten Code.
	 *
	 * @var    array
	 * @access private
	 */
	private $compiled_code = array();

	/**
	 * Enthält den unkompilierten Code.
	 *
	 * @var    array
	 * @access private
	 */
	private $uncompiled_code = array();

	/**
	 * Setzt den Wurzelpfad auf den übergebenen Parameter.
	 *
	 * @param  string  $root Wurzelpfad der gesetzt werden soll.
	 * @access public
	 */
	public function __construct($root = '.') {
		if (!$this->set_rootdir($root)) {
			die("Template->__construct(): Couldn't find Template $root");
		}
	}

	/**
	 * Löscht alle kompilierten Templates.
	 * Sollte aufgerufen werden wenn ein Template geparst worden
	 * ist und ein neues begonnen werden soll.
	 *
	 * @access public
	 */
	public function destroy() {
		$this->_tpldata = array();
	}

	/**
	 * Setzt den Template Wurzelpfad für das aktuelle Template.
	 *
	 * @param  string  $root Wurzelpfad der gesetzt werden soll.
	 * @access protected
	 */
	protected function set_rootdir($dir) {
		if (!is_dir($dir)) {
			return false;
		}

		$this->root = $dir;
		return true;
	}
	
	/**
	 * Sets the template filenames for handles. $filename_array
	 * should be a hash of handle => filename pairs.
	 */
	public function set_filenames($filename_array) {
		if (!is_array($filename_array)) {
			return false;
		}
		
		reset($filename_array);
		
		while(list($handle, $filename) = each($filename_array))	{
			$this->files[$handle] = $this->make_filename($filename);
		}

		return true;
	}


	/**
	 * Lädt die aktuelle Template Datei, kompiliert sie und führt den kompilierten Code aus.
	 * Der Code wird anschließend direkt ausgegeben.
	 *
	 * @param  string  $root Wurzelpfad der gesetzt werden soll.
	 * @access public
	 * @return bool Gibt bei erfolgreicher Ausgabe true zurück.
	 */

	public function pparse($handle) {
		if (!$this->loadfile($handle)) {
			die("Template->pparse(): Couldn't load template file for handle $handle");
		}

		// actually compile the template now.
		if (!isset($this->compiled_code[$handle]) || empty($this->compiled_code[$handle])) {
			// Actually compile the code now.
			$this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle]);
		}

		// Run the compiled code.
		eval($this->compiled_code[$handle]);
		return true;
	}

	/**
	 * Inserts the uncompiled code for $handle as the
	 * value of $varname in the root-level. This can be used
	 * to effectively include a template in the middle of another
	 * template.
	 * Note that all desired assignments to the variables in $handle should be done
	 * BEFORE calling this function.
	 */
	public function assign_var_from_handle($varname, $handle) {
		if (!$this->loadfile($handle)) {
			die("Template->assign_var_from_handle(): Couldn't load template file for handle $handle");
		}

		// Compile it, with the "no echo statements" option on.
		$_str = "";
		$code = $this->compile($this->uncompiled_code[$handle], true, '_str');

		// evaluate the variable assignment.
		eval($code);
		// assign the value of the generated variable to the given varname.
		$this->assign_var($varname, $_str);

		return true;
	}

	/**
	 * Block-level variable assignment. Adds a new block iteration with the given
	 * variable assignments. Note that this should only be called once per block
	 * iteration.
	 */
	public function assign_block_vars($blockname, $vararray) {
		if (strstr($blockname, '.')) {
			// Nested block.
			$blocks = explode('.', $blockname);
			$blockcount = sizeof($blocks) - 1;
			$str = '$this->_tpldata';
			for ($i = 0; $i < $blockcount; $i++) {
				$str .= '[\'' . $blocks[$i] . '.\']';
				eval('$lastiteration = sizeof(' . $str . ') - 1;');
				$str .= '[' . $lastiteration . ']';
			}
			// Now we add the block that we're actually assigning to.
			// We're adding a new iteration to this block with the given
			// variable assignments.
			$str .= '[\'' . $blocks[$blockcount] . '.\'][] = $vararray;';

			// Now we evaluate this assignment we've built up.
			eval($str);
		}
		else {
			// Top-level block.
			// Add a new iteration to this block with the variable assignments
			// we were given.
			$this->_tpldata[$blockname . '.'][] = $vararray;
		}

		return true;
	}

	/**
	 * Root-level variable assignment. Adds to current assignments, overriding
	 * any existing variable assignment with the same name.
	 */
	public function assign_vars($vararray) {
		reset ($vararray);
		while (list($key, $val) = each($vararray)) {
			$this->_tpldata['.'][0][$key] = $val;
		}

		return true;
	}

	/**
	 * Root-level variable assignment. Adds to current assignments, overriding
	 * any existing variable assignment with the same name.
	 */
	public function assign_var($varname, $varval) {
		$this->_tpldata['.'][0][$varname] = $varval;

		return true;
	}


	/**
	 * Generates a full path+filename for the given filename, which can either
	 * be an absolute name, or a name relative to the rootdir for this Template
	 * object.
	 */
	protected function make_filename($filename) {
		// Check if it's an absolute or relative path.
		if (substr($filename, 0, 1) != '/') {
       			$filename = ($rp_filename = $this->root . '/' . $filename) ? $rp_filename : $filename;
		}

		if (!file_exists($filename)) {
			die("Template->make_filename(): Error - file $filename does not exist");
		}

		return $filename;
	}


	/**
	 * If not already done, load the file for the given handle and populate
	 * the uncompiled_code[] hash with its code. Do not compile.
	 */
	protected function loadfile($handle) {
		// If the file for this handle is already loaded and compiled, do nothing.
		if (isset($this->uncompiled_code[$handle]) && !empty($this->uncompiled_code[$handle])) {
			return true;
		}

		// If we don't have a file assigned to this handle, die.
		if (!isset($this->files[$handle])) {
			die("Template->loadfile(): No file specified for handle $handle");
		}

		$filename = $this->files[$handle];

		$str = implode("", @file($filename));
		if (empty($str)) {
			die("Template->loadfile(): File $filename for handle $handle is empty");
		}

		$this->uncompiled_code[$handle] = $str;

		return true;
	}



	/**
	 * Compiles the given string of code, and returns
	 * the result in a string.
	 * If "do_not_echo" is true, the returned code will not be directly
	 * executable, but can be used as part of a variable assignment
	 * for use in assign_code_from_handle().
	 */
	protected function compile($code, $do_not_echo = false, $retvar = '') {
		// replace \ with \\ and then ' with \'.
		$code = str_replace('\\', '\\\\', $code);
		$code = str_replace('\'', '\\\'', $code);

		// change template varrefs into PHP varrefs

		// This one will handle varrefs WITH namespaces
		$varrefs = array();
		preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);
		$varcount = sizeof($varrefs[1]);
		for ($i = 0; $i < $varcount; $i++) {
			$namespace = $varrefs[1][$i];
			$varname = $varrefs[3][$i];
			$new = $this->generate_block_varref($namespace, $varname);

			$code = str_replace($varrefs[0][$i], $new, $code);
		}

		// This will handle the remaining root-level varrefs
		$code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->_tpldata[\'.\'][0][\'\1\']) ) ? $this->_tpldata[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);

		// Break it up into lines.
		$code_lines = explode("\n", $code);

		$block_nesting_level = 0;
		$block_names = array();
		$block_names[0] = ".";

		// Second: prepend echo ', append ' . "\n"; to each line.
		$line_count = sizeof($code_lines);
		for ($i = 0; $i < $line_count; $i++) {
			$code_lines[$i] = chop($code_lines[$i]);
			if (preg_match('#<!-- BEGIN (.*?) -->#', $code_lines[$i], $m)) {
				$n[0] = $m[0];
				$n[1] = $m[1];

				// Added: dougk_ff7-Keeps templates from bombing if begin is on the same line as end.. I think. :)
				if ( preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $n) ) {
					$block_nesting_level++;
					$block_names[$block_nesting_level] = $m[1];
					if ($block_nesting_level < 2) {
						// Block is not nested.
						$code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->_tpldata[\'' . $n[1] . '.\']) ) ?  sizeof($this->_tpldata[\'' . $n[1] . '.\']) : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					} else {
						// This block is nested.

						// Generate a namespace string for this block.
						$namespace = implode('.', $block_names);
						// strip leading period from root level..
						$namespace = substr($namespace, 2);
						// Get a reference to the data array for this block that depends on the
						// current indices of all parent blocks.
						$varref = $this->generate_block_data_ref($namespace, false);
						// Create the for loop code to iterate over this block.
						$code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}

					// We have the end of a block.
					unset($block_names[$block_nesting_level]);
					$block_nesting_level--;
					$code_lines[$i] .= '} // END ' . $n[1];
					$m[0] = $n[0];
					$m[1] = $n[1];
				} else {
					// We have the start of a block.
					$block_nesting_level++;
					$block_names[$block_nesting_level] = $m[1];
					if ($block_nesting_level < 2)
					{
						// Block is not nested.
						$code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->_tpldata[\'' . $m[1] . '.\']) ) ? sizeof($this->_tpldata[\'' . $m[1] . '.\']) : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					} else {
						// This block is nested.

						// Generate a namespace string for this block.
						$namespace = implode('.', $block_names);
						// strip leading period from root level..
						$namespace = substr($namespace, 2);
						// Get a reference to the data array for this block that depends on the
						// current indices of all parent blocks.
						$varref = $this->generate_block_data_ref($namespace, false);
						// Create the for loop code to iterate over this block.
						$code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
				}
			}
			else if (preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $m)) {
				// We have the end of a block.
				unset($block_names[$block_nesting_level]);
				$block_nesting_level--;
				$code_lines[$i] = '} // END ' . $m[1];
			} else {
				// We have an ordinary line of code.
				if (!$do_not_echo) {
					$code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';
				} else {
					$code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";';
				}
			}
		}

		// Bring it back into a single string of lines of code.
		$code = implode("\n", $code_lines);
		return $code	;

	}


	/**
	 * Generates a reference to the given variable inside the given (possibly nested)
	 * block namespace. This is a string of the form:
	 * ' . $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
	 * It's ready to be inserted into an "echo" line in one of the templates.
	 * NOTE: expects a trailing "." on the namespace.
	 */
	protected function generate_block_varref($namespace, $varname) {
		// Strip the trailing period.
		$namespace = substr($namespace, 0, strlen($namespace) - 1);

		// Get a reference to the data block for this namespace.
		$varref = $this->generate_block_data_ref($namespace, true);
		// Prepend the necessary code to stick this in an echo line.

		// Append the variable reference.
		$varref .= '[\'' . $varname . '\']';

		$varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';

		return $varref;

	}


	/**
	 * Generates a reference to the array of data values for the given
	 * (possibly nested) block namespace. This is a string of the form:
	 * $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
	 *
	 * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
	 * NOTE: does not expect a trailing "." on the blockname.
	 */
	protected function generate_block_data_ref($blockname, $include_last_iterator) {
		// Get an array of the blocks involved.
		$blocks = explode(".", $blockname);
		$blockcount = sizeof($blocks) - 1;
		$varref = '$this->_tpldata';
		// Build up the string with everything but the last child.
		for ($i = 0; $i < $blockcount; $i++)
		{
			$varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
		}
		// Add the block reference for the last child.
		$varref .= '[\'' . $blocks[$blockcount] . '.\']';
		// Add the iterator for the last child if requried.
		if ($include_last_iterator)
		{
			$varref .= '[$_' . $blocks[$blockcount] . '_i]';
		}

		return $varref;
	}

}

?>