<?php

function pc_lazy_load_compat_wpprint() {
	if ( 1 == intval( get_query_var( 'print' ) ) || 1 == intval( get_query_var( 'printpage' ) ) ) {
		add_filter( 'pc_lazy_load_enabled', '__return_false' );
	}
}

add_action( 'pc_lazy_load_compat', 'pc_lazy_load_compat_wpprint' );
