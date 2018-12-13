<?php

namespace daophp\responser;

/**
 * all the views must implement this interface to rend a result to display
 *
 */
interface Renderable {
	public function render( $renderData );
}


?>