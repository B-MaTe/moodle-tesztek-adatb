<?php

namespace enum;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case TEACHER = 'TEACHER';
    case USER = 'USER';
    case ANONYMOUS = 'ANONYMOUS';
}
