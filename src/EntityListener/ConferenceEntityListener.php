<?php


namespace App\EntityListener;

use App\Entity\Conference;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Conference Entity Listener
 * 
 * @author Martin Seon
 * @package App\EntityListener
 */
class ConferenceEntityListener
{
    /**
     * 
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * Class constructor.
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Conference $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger);
    }

    public function preUpdate(Conference $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger);
    }
}

/** End of ConferenceEntityListener.php */
