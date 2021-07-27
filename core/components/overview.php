<?php
art('table');
?>
<div class="page_head">
    <div class="title"><?php E('Overview'); ?></div>
</div>
<div class="overview">
    <div class="row mb20">
        <div class="col">
            <div class="widget">
                <div class="title"><?php E('Hardware Statistics'); ?></div>
                <div class="body">

                </div>
            </div>
        </div>
        <div class="col">
            <div class="widget">
                <div class="title"><?php E('Software Statistics'); ?></div>
                <div class="body p0">
                    <table class="plain s bsn">
                        <tbody>
                        <tr>
                            <td><?php E('File Upload Max Size Limit'); ?></td>
                            <td><?php echo ini_get('post_max_size'); ?></td>
                        </tr>
                        <tr>
                            <td><?php E('PHP Version'); ?></td>
                            <td><?php echo defined('PHP_VERSION_ID') ? str_replace('0','.',PHP_VERSION_ID) : ''; ?></td>
                        </tr>
                        <tr>
                            <td><?php E('MySQL Version'); ?></td>
                            <td><?php
                                $output = shell_exec('mysql -V');
                                preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
                                echo !empty( $version ) ? $version : '-';
                                ?></td>
                        </tr>
                        <tr>
                            <td><?php E('AIO Version'); ?></td>
                            <td><?php $aio_v = include ROOTPATH . 'version.php'; echo $aio_v; ?></td>
                        </tr>
                        <tr>
                            <td><?php E('Server OS'); ?></td>
                            <td><?php echo PHP_OS_FAMILY; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div><?php E('RAM'); ?></div>
    </div>
</div>