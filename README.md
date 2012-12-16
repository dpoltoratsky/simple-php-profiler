Simple PHP Profiler
===================

This is the tool to profile any code you have.

This initial version contains only time profiling for now.

Example
--------

This simple example demonstrates basic usage of profiler.

    <?php
        require_once 'Profiler.php';
    
        class ToProfile {
        
            protected $profiler;


            public function __construct() {
                $this->profiler = new Profiler( './toProfile' );                
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

This code will generate _./toProfile/methodToProfile.log_ file with following content:

    1355658919 (2012.12.16 15:03:19) 923325723 1 Execution started 1355658919.1375 sec
    1355658920 (2012.12.16 15:03:20) 923325723 2 Before some long code... 1.0012290477753 sec
    1355658923 (2012.12.16 15:03:23) 923325723 3 After some long code... 3.0012099742889 sec
    1355658924 (2012.12.16 15:03:24) 923325723 4 Execution is complete. Total time is  5.0037221908569 sec    
