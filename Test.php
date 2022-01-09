<?php

class Test
{
    public function voidReturn(): void
    {
        $type = (new \ReflectionMethod($this, __FUNCTION__))->getReturnType();
        if ($type !== "void") {
            return $type;
        } else {
            echo $type;
            return;
        }
    }
}

(new Test)->voidReturn();
