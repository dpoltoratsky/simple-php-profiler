<?php



/**
 * Measurer is a profiling session
 */
class ProfilingSession {
    
    /**
     * Instance of profile that have this session started.
     * @var Spp 
     */
    protected $profiler;
    
    /**
     * Unique key for profiling session. Will be used as log file name with 
     * profiling results.
     * @var string 
     */
    protected $key;
    
    /**
     * Full path to log with profiling results.
     * @var string 
     */
    protected $filePath;
    
    /**
     * Time of profiling session start (including milliseconds).
     * @var float 
     */
    protected $startTime;
    
    /**
     * Time of lats measurement (including milliseconds).
     * @var float 
     */
    protected $latsStepTime;
    
    /**
     * Index number of current profiling step (first is 1).
     * @var int
     */
    protected $currentStep;
    
    /**
     * Unique identifier of profiling session. This differs $this->key so that 
     * many sessions can be started (multipe http sessions at the same time) but 
     * this will be unique for each of them. 
     * For generation mechanism  please see {@link http://php.net/manual/ru/function.mt-rand.php}
     * @var int
     */
    protected $id;
       
    
    protected $messages = '';
    
    
    /**
     * Default message to be logged when profilng session will be finished
     */
    const DEFAULT_STOP_MESSAGE = 'Execution is complete. Total time is ';    
    
    /**
     * Default message to be be logged when profiling session starts
     */
    const DEFAULT_START_MESSAGE = 'Execution started';
    
    
    /**
     * 
     * @param Spp $profiler Instance of profiler that has this session started.
     * @param string $key Unique key for profiling session.
     * @param string $message Custom start message. If not specified will be used default one. 
     * @see Measurer::DEFAULT_START_MESSAGE
     */
    public function __construct( 
        Spp $profiler, 
        $key, 
        $message = '' 
    ) {
        
        $this->profiler = $profiler;
        $this->key = $key;
        $this->filePath = $this->initFilePath();
        
        // TODO make this unique for sure
        $this->id = mt_rand();
        
        $this->startTime = $this->getTime();
        $this->latsStepTime = $this->startTime;
        $this->currentStep = 1;
        
        if ( empty( $message ) ) {
            $message = self::DEFAULT_START_MESSAGE;
        }
        
        $this->step( $message );
    }
    
    
    /**
     * Flushed messages when object destroyed
     */
    public function __destruct() {
        $this->flushMessages();
    }
    
    
    /**
     * Flushes all measurements results to file.
     */
    protected function flushMessages() {
        
        if ( !empty( $this->messages ) ) {
            $filePointer = fopen( $this->filePath, 'a' );

            fwrite( $filePointer, $this->messages );

            fclose( $filePointer );    
            
            $this->messages = '';
        }
    }
    
    
    /**
     * Returns current time stamp including milliseconds.
     * 
     * @return float
     */
    protected function getTime() {
        return microtime(true);
    }
    
    
    /**
     * Writes results of measurement to file.
     * 
     * @param string $message Message to log. Will be prepended with timestamp and id of profiling session.
     */
    protected function logMeasurement( $message ) {
        $message = sprintf( 
            '%s %s %s',            
            $this->getTimeStamp(),
            $this->id,
            $message
        );
    
        $this->messages .= $message.PHP_EOL;        
    }
    
    
    /**
     * Creates log file if it is not exists and returns full path to it.
     * 
     * @return string
     */
    protected function initFilePath() {
        
        $result = $this->profiler->getLogsPath();
        
        $result .= $this->key.'.log';
        
        if ( !file_exists( $result ) ) {
            touch( $result );
        }
        
        return $result;
    }
    
    
    /**
     * Indicates last step of profiling session. Logs information about total 
     * time spent for entire profiling session.
     * 
     * @param string $message Custom stop message. If not specified will be used default one.
     * @see Measurer::DEFAULT_STOP_MESSAGE
     */
    public function stop( $message = '' ) {
        
        if ( empty( $message ) ) {
            $message = self::DEFAULT_STOP_MESSAGE;
        }
        
        $this->step( $message, true );
        
        $this->flushMessages();
    }
    
    
    /**
     * Performs new measurement and logs results.
     * 
     * @param string $message Description of current measurement step.
     * @param bool $final Indicates ending of profiling session.
     */
    public function step( $message = '', $final = false ) {
        
        if ( $this->currentStep == 1 ) {
            $stepTime = $this->startTime;
            
        } else if ( $final ) {
            $stepTime = $this->getFinalTime();
            
        } else {
            $stepTime = $this->getStepTime();            
        }
       
        $message = sprintf( 
            '%s %s %s sec',
            $this->currentStep,
            $message,
            $stepTime
        );
        
        $this->currentStep++;
        
        $this->logMeasurement( $message );
    }
    
    
    /**
     * Returns total time spent for entire profiling session.
     * 
     * @return float
     */
    protected function getFinalTime() {
        $currentTime = $this->getTime();
        
        $result = $currentTime - $this->startTime;
        
        return $result;
    }
    
    
    /**
     * Returns time spent since last step.
     * 
     * @return float
     */
    protected function getStepTime() {
        $currentTime = $this->getTime();
        
        $result = $currentTime - $this->latsStepTime;
        
        $this->latsStepTime = $currentTime;
        
        return $result;
    }
    
    
    /**
     * Returns timestamp for logging.
     * 
     * @return string
     */
    protected function getTimeStamp() {
        return time().' ('.strftime( '%Y.%m.%d %H:%I:%S', time() ).')';
    }
}

?>
