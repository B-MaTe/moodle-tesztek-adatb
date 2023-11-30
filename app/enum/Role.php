<?php

namespace enum;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
    case ANONYMOUS = 'ANONYMOUS';
}
