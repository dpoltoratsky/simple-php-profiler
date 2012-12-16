Simple PHP Profiler
===================

This is the tool to profile any code you have.

Examples
--------

    <?php
        require_once 'Profiler.php';
    
        class ToProfile {
        
            protected $profiler;


            public function __construct() {
                $this->profiler = new Profiler( './toprofile' );                
            }


            public function methodToProfile() {
                $profilingSession = $this->profiler->start( 'methodToProfile' );

                sleep(1);

                $profilingSession->step( 'Before some long playing code...' );

                sleep(3);

                $profilingSession->step( 'After some long playing code...' );

                sleep(1);

                $profilingSession->stop();

            }
        }

        $toProfile = new ToProfile();
        
        $toProfile->methodToProfile();

    ?>