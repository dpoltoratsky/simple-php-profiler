<?php

require_once 'ProfilingSession.php';



/**
 * Simple Profiler
 * 
 */
class Profiler {

    /**
     * Path to the folder that will contain profiling logs. Will be set in 
     * constructor.
     * @var string logsPath
     */
    protected $logsPath;
    
    /**
     * Set of profilingSessions that were started. Contains instances of type ProfilingSession.
     * @var Array
     */
    protected $profilingSessions = Array();
    
    
    /**
     * @param string $logsPath Path to the folder that will contain profiling logs.
     */
    public function __construct( $logsPath ) {    
        $this->logsPath = $this->initLogsPath( $logsPath );
    }   
        

    /**
     * 
     * @return string
     */
    public function getLogsPath() {
        return $this->logsPath;
    }
    
    
    /**
     * Creates log path if it not exists.
     * 
     * @param string $logsPath Path to check.
     * @return string Path to logs folder with trailing '/'.
     */
    protected function initLogsPath( $logsPath ) {
        
        $logsPath = rtrim( $logsPath, '/' ).'/';
        
        if ( !file_exists( $logsPath ) ) {
            
            //TODO make mode configurable
            mkdir( $logsPath, 0777, true );
        }        

        // TODO add logic to check if derectory is writable
        
        return $logsPath;
    }
    
    
    /**
     * Starts profiling session.
     * 
     * @param string $key Unique key for profiling session.
     * @param string $message Custom start message. If not specified will be used default one.
     * 
     * @return ProfilingSession Instance of measurer to operate with.
     */
    public function start( 
        $key, 
        $message = '' 
    ) {
        
        // TODO handle exceptional cases
        
        $this->profilingSessions[$key] = new ProfilingSession( $this, $key, $message );
        return $this->profilingSessions[$key];
    }
    
    
    /**
     * This is a decorator for the ProfilingSession::step() method.
     * 
     * @param string $key Unique key for profiling session.
     * @param string $message Message to describe current step.
     */
    public function step( 
        $key, 
        $message = '' 
    ) {
        
        // TODO handle exceptional cases
        
        if ( isset( $this->profilingSessions[$key] ) ) {
            $this->profilingSessions[$key]->step( $message ); 
        }        
    }
            
    
    /**
     * This is a decorator for the ProfilingSession::stop() method.
     * 
     * @param string $key Unique key for profiling session.
     * @param string $message Custom stop message. If not specified will be used default one.
     */
    public function stop( 
        $key, 
        $message = '' 
    ) {
       
        // TODO handle exceptional cases
        
        if ( isset( $this->profilingSessions[$key] ) ) {
            $this->profilingSessions[$key]->stop( $message ); 
        }
    }
}

?>