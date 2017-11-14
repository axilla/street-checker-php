<?php

namespace AppBundle\Helpers;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;


class TextGeneratorHelper
{
    
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * Get title from examples 
     * @param int $index  - from 0 to 100
     * @return string
     */
    public function getTitle($index)
    {
        $examples = array(
            'Ut tempor risus ante',
            'Lorem ipsum dolor sit amet',
            'Cras rutrum nibh et quam',
            'Cras Rutrum',
            'Lorem ipsum dolor',
            'Sed tempor-malesuada',
            'Congue tempus lectus',
            'Phasellus congue tempus',
            'Vestibulum ante ipsum primis',
            'Proin tristique Phasellus',
            'Proin laoreet magna quis  Phasellus',
            'Pellentesque semper',
            'Class aptent taciti sociosqu ad',
            'Aliquam at tortor',
            'Aenean malesuada',
            );
        
        $key = $index % (count($examples ) - 1);
        return $examples[$key];
    }
    
    /**
     * Get title from examples 
     * @param int $index  - from 0 to 100
     * @return string
     */
    public function getParagraph($index)
    {
        $examples = array(
            'Vivamus eros mi, consequat nec mollis sit amet, venenatis vitae lorem. Ut sem dui, egestas eget turpis id, cursus iaculis odio. Proin efficitur risus sit amet ornare accumsan. Quisque volutpat pellentesque libero ut vulputate. Nunc ac arcu at leo elementum ultrices. Pellentesque sit amet molestie libero. Praesent a condimentum lectus.',
            'Donec malesuada ligula augue, ut pulvinar lacus vulputate id. Nunc ornare ante turpis, eget dignissim odio ornare at. Fusce tellus urna, cursus vel ex in, ullamcorper auctor dolor. Nunc posuere dapibus dictum. Suspendisse placerat, odio non dignissim lobortis, felis felis rhoncus massa, et auctor dolor odio eget nulla. Nullam vestibulum.',
            'Duis felis libero, consectetur in accumsan ac, pharetra vel tortor. Curabitur placerat nunc elit, lobortis bibendum arcu condimentum vel. Nullam neque diam, dignissim at urna sed, tristique elementum libero. Curabitur at ante nulla. Ut semper hendrerit tempus. Vivamus ullamcorper non erat vel convallis. In hac habitasse platea dictumst. Proin interdum.',
            'Aliquam sit amet dolor quis augue pellentesque rutrum in ut risus. Vestibulum felis metus, ullamcorper vel magna id, pellentesque gravida orci. Morbi sit amet faucibus orci. Class aptent taciti sociosqu.',
            'Fusce facilisis ipsum eget est ullamcorper, nec placerat elit porta. Fusce mattis tellus quis congue molestie. Duis gravida eros sit amet nulla tincidunt, imperdiet eleifend nibh maximus. Vivamus nulla sem.',
            'Vestibulum viverra libero risus, in suscipit urna luctus eu. Cras vel diam pretium, rhoncus sem.',
            'Vestibulum et nibh id nulla mollis posuere. Sed ultricies sem id massa finibus tincidunt. In bibendum ante ante, vitae pulvinar purus mattis non. Curabitur vitae tristique urna, nec elementum nulla. Aenean eget sapien consequat, tristique mi laoreet, porta ante. Cras urna massa, hendrerit at maximus id, tincidunt iaculis orci. Quisque vel arcu efficitur, blandit lectus id, ultrices libero. Aenean sit amet gravida nisi, nec elementum arcu. Nullam eu ornare dui.',
            'Maecenas non ante ex. Suspendisse mollis ornare augue, quis dapibus tellus vestibulum vitae. Pellentesque vel turpis consectetur, rhoncus nunc sit amet, venenatis sapien. Nulla consectetur leo risus, vel accumsan ipsum scelerisque ac. Morbi quis dui ut nisi tristique pretium. Aliquam ultrices velit rutrum, bibendum velit ac, tincidunt dui. Nulla convallis lacinia enim id sodales. Phasellus efficitur ultrices facilisis. Duis sagittis orci purus, sit amet tincidunt urna commodo a. Nullam ornare.',
            'In lobortis, dolor a cursus tempor, augue leo congue massa, ac vehicula orci lacus vel mauris. Maecenas pellentesque arcu in.',
            'Duis magna lorem, consectetur sed diam in, cursus vulputate turpis. Curabitur sodales auctor facilisis. Integer vel tempor nisl, eget imperdiet.',
            'Aenean lacinia dignissim ante, at vehicula velit vehicula et. Duis dignissim orci in commodo convallis. Sed semper tortor gravida tristique.',
            'Maecenas et dignissim odio, quis elementum urna. Integer lorem dolor, laoreet non ullamcorper eget, blandit eu enim. Donec molestie a eros a interdum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Cras a posuere nunc. Donec vitae lorem in orci varius lobortis. Aenean mattis lectus urna. Sed ultrices vulputate neque, vitae finibus dolor tincidunt a. Donec ac velit nec purus ultrices fermentum.
                <br>Morbi sollicitudin laoreet nulla, quis blandit risus ultricies nec. Aliquam eget felis arcu. Donec volutpat, est vitae varius pellentesque, lectus lorem tempus tortor, ut ornare ipsum nisi sed dolor. Nam vel mauris maximus.',
            'Integer ut purus sed nibh ultricies laoreet. Duis et porttitor risus. Quisque pretium quam facilisis lectus condimentum mattis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis.',
            'Phasellus elit est, commodo vitae rhoncus id, ultrices ut velit. In ut sagittis ligula. Duis tristique lectus nisi. Cras egestas eleifend rhoncus. Donec eu aliquet quam. Pellentesque vel elementum tellus.',
            'Suspendisse potenti. In risus mauris, fermentum sed lorem id, fringilla feugiat risus. Integer pharetra vestibulum elit eu pulvinar. Mauris quam urna, tristique eget felis in, efficitur auctor nulla. Sed sed cursus sem. Donec porta, mi non dapibus euismod, ipsum orci semper ante, at vestibulum purus elit et lacus. Donec gravida congue neque, nec dignissim lacus dapibus eu. Vestibulum eu nibh ultricies, cursus arcu vitae, convallis risus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In vulputate id enim sed pharetra. Sed consequat ultricies elit, in semper ex dictum non. Integer elementum ante quam, sed fermentum metus dictum nec. Cras dui odio, fringilla et ultricies sed, tincidunt nec elit. Nulla ultrices, mi in pulvinar scelerisque, elit diam molestie urna, id semper elit est vitae neque.
                <br>Etiam massa enim, hendrerit at felis eget, dictum consectetur erat. Fusce sed tellus ut purus accumsan hendrerit. Morbi vestibulum leo non nisl sagittis tempor. Sed blandit dictum cursus. Fusce accumsan turpis eget tellus sollicitudin, et scelerisque mi varius. Donec eget leo tincidunt diam pulvinar placerat. Curabitur bibendum ante ac dapibus aliquam. Nulla varius, tellus eget bibendum mattis, augue purus tincidunt ex, in placerat diam justo vitae velit. Nulla vel enim et erat ultricies efficitur ut nec felis. Nullam nisi purus, finibus non arcu ac, venenatis convallis est. Praesent ante turpis, tempor vitae velit ut, scelerisque tristique enim. Nam consequat nibh in lectus scelerisque, eget gravida eros rutrum. Nullam quis commodo lectus, ac posuere erat. Cras et rhoncus neque. Sed auctor quam eget ipsum feugiat, a eleifend mi vestibulum.
                <br>Suspendisse vulputate velit ac tempus semper. Pellentesque at facilisis massa, sit amet volutpat turpis. Aliquam dictum quam sit amet lectus fermentum, a accumsan metus dignissim. Etiam luctus at ante id eleifend. Sed non leo lectus. Aenean molestie placerat mollis. Suspendisse urna odio, rhoncus id lorem eu, mollis efficitur est. Ut viverra, erat id molestie malesuada, lorem tellus commodo mi, auctor laoreet augue risus sed mauris. Nullam commodo in purus vitae viverra. Integer eget auctor metus. Nam euismod tempus mollis.',
            );
        
        $key = $index % (count($examples ) - 1);
        return $examples[$key];
    }
}
