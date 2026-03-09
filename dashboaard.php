<?php
/**
 * Dashboard Template using Tailwind CSS via CDN
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'FlyWP_API' ) ) {
    echo '<div class="wrap"><div class="error"><p>' . esc_html__( 'API class is missing.', 'flywp-waas' ) . '</p></div></div>';
    return;
}

$api = new FlyWP_API();
$sites_response = $api->get_sites();
$sites = is_wp_error( $sites_response ) ? [] : (isset($sites_response['data']) ? $sites_response['data'] : []);
$blueprints_response = $api->get_blueprints();
$blueprints = is_wp_error( $blueprints_response ) ? [] : (isset($blueprints_response['data']) ? $blueprints_response['data'] : []);

?>
<!-- Load Tailwind CSS via CDN for the modern SaaS look -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Wrap everything in a specific div to avoid conflicting with WP Admin styles too much -->
<div class="wrap m-0 p-6 bg-gray-50 min-h-screen font-sans flywp-waas-dashboard">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?php esc_html_e( 'FlyWP Sites', 'flywp-waas' ); ?></h1>
                <p class="text-gray-500 mt-1"><?php esc_html_e( 'Manage your WaaS network.', 'flywp-waas' ); ?></p>
            </div>
            <button onclick="document.getElementById('create-site-modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <?php esc_html_e( 'Create New Site', 'flywp-waas' ); ?>
            </button>
        </div>

        <!-- Alerts -->
        <?php if ( isset( $_GET['success'] ) && $_GET['success'] === 'site_created' ) : ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-sm text-green-700"><?php esc_html_e( 'Site creation triggered successfully!', 'flywp-waas' ); ?></p>
            </div>
        <?php endif; ?>

        <?php if ( is_wp_error( $sites_response ) ) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <?php echo esc_html( $sites_response->get_error_message() ); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sites Table -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php esc_html_e( 'Site Name', 'flywp-waas' ); ?></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php esc_html_e( 'Domain', 'flywp-waas' ); ?></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php esc_html_e( 'Status', 'flywp-waas' ); ?></th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><?php esc_html_e( 'Actions', 'flywp-waas' ); ?></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ( empty( $sites ) || ! is_array( $sites ) ) : ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <?php esc_html_e( 'No sites found. Create your first site to get started.', 'flywp-waas' ); ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $sites as $site ) : ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo esc_html( $site['name'] ?? 'Unknown' ); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo esc_html( $site['domain'] ?? 'N/A' ); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?php echo esc_html( $site['status'] ?? 'Active' ); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900"><?php esc_html_e( 'Manage', 'flywp-waas' ); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Site Modal -->
    <div id="create-site-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('create-site-modal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="flywp_create_site">
                    <?php wp_nonce_field( 'flywp_create_site_nonce', 'flywp_nonce' ); ?>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    <?php esc_html_e( 'Create New Site', 'flywp-waas' ); ?>
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="site_name" class="block text-sm font-medium text-gray-700"><?php esc_html_e( 'Site Name', 'flywp-waas' ); ?></label>
                                        <input type="text" name="site_name" id="site_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" required>
                                    </div>
                                    <div>
                                        <label for="blueprint_id" class="block text-sm font-medium text-gray-700"><?php esc_html_e( 'Blueprint', 'flywp-waas' ); ?></label>
                                        <select id="blueprint_id" name="blueprint_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                            <option value=""><?php esc_html_e( 'Select a blueprint...', 'flywp-waas' ); ?></option>
                                            <?php if ( ! empty( $blueprints ) && is_array( $blueprints ) ) : ?>
                                                <?php foreach ( $blueprints as $bp ) : ?>
                                                    <option value="<?php echo esc_attr( $bp['id'] ); ?>"><?php echo esc_html( $bp['name'] ); ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="1">E-commerce Template (Demo)</option>
                                                <option value="2">Blog Template (Demo)</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                            <?php esc_html_e( 'Create Site', 'flywp-waas' ); ?>
                        </button>
                        <button type="button" onclick="document.getElementById('create-site-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                            <?php esc_html_e( 'Cancel', 'flywp-waas' ); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
