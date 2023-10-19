<?php
class bootstrap {
  
    public $attributes = [
        'title'     => 'my Default Title',
        'content'   => [],
        'style'     => [],
        'menu'      => [],
        'alert'     => [ '','']
    ];

    function RA($a,$p,$d = null) {
        if(isset($a[$p])) {
            return $a[$p];
        } else {
            return $d;
        }
    }

    function content($c) {
        array_push($this->attributes['content'],$c);
    }

    function style($c) {
        array_push($this->attributes['style'],$c);
    }

    function object($KW = []) {
        $tag = $KW['tag'];
        $content = $this->RA($KW,'content');

        $P = '';

        if(isset($KW['param'])) {
            foreach ($KW['param'] as $key => $value) {
                if($key == 'required' and $value) {
                    $P .= ' required';
                } else {
                    if(is_array($value)) {
                        $y = join(" ",$value);
                    } else {
                        $y = $value;
                    }
                    $P .= " $key=\"$y\"";
                }
            }
        }

        if($content != null || $tag == 'textarea') {
            return "<$tag$P>$content</$tag>\n";
        } else {
            return "<$tag$P />\n";
        }
    }

    function alert($c,$t) {
        $this->attributes['alert'] = [ $c, $t ];
    }

    function att($key,$value) {
        $this->attributes[$key] = $value;
    }

    function menu($txt, $link, $parent = null) {
        if(isset($_SESSION['_csrf'])) {
            if(strpos(" $link","?") > 0 ) {
                $link .= '&';
            } else {
                $link .= '?';
            }
            $link .= '_csrf=' . $_SESSION['_csrf'];
        }

        if($parent == null) {
            $this->attributes['menu'][$txt] = $link;
        } else {
            $this->attributes['menu'][$parent][$txt] = $link;
        }
    }

    function render() {
        // ============== Header
        ?>
        <!doctype html>
        <html lang="en">
        <head>
        <?php
            if(file_exists("resources/bootstrap.min.css")) {
                print "<link href=\"resources/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor\" crossorigin=\"anonymous\">";

            } else {
                print "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor\" crossorigin=\"anonymous\">";
            }
        ?>

            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta charset="UTF-8">
            <title>
                <?php echo $this->attributes['title']; ?>
            </title>
            <style>
                <?php echo  join("\n",$this->attributes['style']); ?>
            </style>
        </head>
        <body>
            <?php
            if(file_exists("resources/popper.min.js")) {
                print "<script src=\"resources/popper.min.js\" integrity=\"sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB\" crossorigin=\"anonymous\"></script>";

            } else {
                print "<script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js\" integrity=\"sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB\" crossorigin=\"anonymous\"></script>";
            }
        
            if(file_exists("resources/bootstrap.bundle.min.js")) {
                print "<script src=\"resources/bootstrap.bundle.min.js\" integrity=\"sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2\" crossorigin=\"anonymous\"></script>";
            } else {
                print "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2\" crossorigin=\"anonymous\"></script>";
            }

            if(file_exists("resources/jquery-3.6.0.min.js")) {
                print "<script src=\"resources/jquery-3.6.0.min.js\" integrity=\"sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=\" crossorigin=\"anonymous\"></script>";
            } else {
                print "<script src=\"https://code.jquery.com/jquery-3.6.0.min.js\" integrity=\"sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=\" crossorigin=\"anonymous\"></script>";
            }

            if(file_exists("resources/jquery-ui-git.js")) {
                print "<script src=\"resources/jquery-ui-git.js\" crossorigin=\"anonymous\"></script>";
            } else {
                print "<script src=\"https://releases.jquery.com/git/ui/jquery-ui-git.js\" crossorigin=\"anonymous\"></script>";
            }

        // ============== menu
        print $this->render_menu($this->attributes['menu']);

        // ============== alerts
        $alerts = [
            'success'   => '&#9989;',
            'info'      => '&#8505;',
            'warning'   => '&#9888;',
            'danger'    => '&#128721;'
        ];

        if(isset($alerts[$this->attributes['alert'][0]])) {
            $a = $this->attributes['alert'][0];
            $icon = $alerts[$a];

            print $this->object([
                'tag'   => 'div',
                'param' => [
                    'class' => [
                        'alert',
                        "alert-$a",
                        'w-50',
                        'mx-auto',
                        'alert-dismissible',
                        'fade',
                        'show'
                    ]
                ],
                'content' => $this->object([
                    'tag'   => 'strong',
                    'content'   => $icon
                ]) .
                $this->attributes['alert'][1] .

                $this->object([
                    'tag'   => 'button',
                    'param' => [
                        'type'              => 'button',
                        'class'             => 'btn-close',
                        'data-bs-dismiss'   => 'alert'
                    ]
                ])
            ]);
        }

        // ============== content
        print join("\n",$this->attributes['content']);

        // ============== footer
        //print $this->object([
        //    'tag'   => 'footer',
        //    'param' => [
        //        'class' => [
        //            'bg-light',
        //            'text-center',
        //            'text-lg-start'
        //        ],
        //    ],
        //    'content' => $this->object([
        //        'tag'   => 'div',
        //        'param' => [
        //            'class' => [
        //                'text-center',
        //                'p-3'
        //            ],
        //            'style' => 'background-color: rgba(0, 0, 0, 0.2);'
        //        ],
        //        'content'   => $this->attributes['footer']
        //    ])
        //]);
  
        print "</body>\n";
        print "</html>";
    }

    function elements($param = []) {
        $e = '<b>UNKNOWN TYPE in elements</b>';

        if(!isset($param['required'])) {
            $param['required'] = False;
        }

        if($param['type'] == 'text') {
            $e = $this->object([
                'tag' => 'input',
                'param' => [
                    'type'          => 'text',
                    'class'         => [ 'form-control', ( $param['required'] == True ? 'is-invalid' : '' ) ],
                    'name'          => $this->RA($param,'name'),
                    'value'         => $this->RA($param,'value'),
                    'placeholder'   => $this->RA($param,'placeholder')

                ]
            ]);
        } elseif ($param['type'] == 'password') {
            $e = $this->object([
                'tag' => 'input',
                'param' => [
                    'type' => 'password',
                    'class'         => [ 'form-control', ( $param['required'] == True ? 'is-invalid' : '' ) ],
                    'name' => $this->RA($param,'name'),
                    'placeholder' => $this->RA($param,'placeholder')
                ]
            ]);
        } elseif ($param['type'] == 'submit') {
            $e = $this->object([
                'tag' => 'button',
                'param' => [
                    'type' => 'submit',
                    'class' => [
                        'btn',
                        'btn-primary',
                        'btn-block',
                    ],
                ],
                'content' => $param['text']
            ]);
        } elseif ($param['type'] == 'button') {
            $e = $this->object([
                'tag'   => 'button',
                'param' => [
                    'class' => [
                        'btn',
                        isset($param['btn']) ? $param['btn'] : 'btn-primary',
                        'btn-block'
                    ]
                ],
                'content' => $param['text']
            ]);
        } elseif ($param['type'] == 'checkbox') {
            $e = $this->object([
                'tag'   => 'input',
                'param' => [
                    'name'  => $param['name'],
                    'type'  => 'checkbox',
                    'value' => $this->RA($param,'value')
                ],
            ]);
        } elseif($param['type'] == 'hidden') {
            $e = $this->object([
                'tag'   => 'input',
                'param' => [
                    'type'  => 'hidden',
                    'name'  => $param['name'],
                    'value' => $param['value']
                ],
            ]);
        } elseif($param['type'] == 'dropdown') {
            $ops = '';
            // - 
            $newOpts = [];
            foreach ($param['options'] as $o) {
                if(is_array($o)) {
                    array_push($newOpts,[ $o[0], $o[1] ]);
                } else {
                    array_push($newOpts,[ $o, $o ]);
                }
            }

            // sort the options by their value
            usort($newOpts, function ($a,$b) {
                return strcmp($a[1],$b[1]);
            });

            foreach ($newOpts as $o) {
                $a = htmlentities($o[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $b = htmlentities($o[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                // TODO
                if($a == $this->RA($param,'value')) {
                    $ops .= "<option selected value=\"$a\">$b</option>";
                } else {
                    $ops .= "<option value=\"$a\">$b</option>";
                }
            }

            $e = $this->object([
                'tag'   => 'select',
                'param' =>  [
                    'name'  => $param['name'],
                    'class' => 'form-select'
                ],
                'content'   => $ops
            ]);
        } elseif($param['type'] == 'textarea') {
            $e = $this->object([
                'tag' => 'textarea',
                'param' => [
                    'class' => [ 'form-control', ( $param['required'] == True ? 'is-invalid' : '' ) ],
                    'name'  => $param['name'],
                ],
                'content'   => $this->RA($param,'value')
            ]);
        } elseif($param['type'] == 'readonly') {
            $e = $this->object([
                'tag' => 'p',
                'content'   => $this->RA($param,'value')
            ]);
        }
 
        return $e;
    }

    function table($head,$data,$actions = []) {
        $this->style('
            .data-table {
                width: 75%;
                margin: 50px auto;
                font-size: 15px;
            }
            .data-table table {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 30px;
            }
        ');

        $th = '';

        $orderby = '';
        $order = 0;

        foreach ($head as $key => $value) {
            // Order by Arrows
            $arrow = '';
            if (isset($_GET['_orderby']) && $_GET['_orderby'] == $value) {
                $orderby = $value;

                if(isset($_SESSION['_order'])) {
                    if($_SESSION['_order'] == 1) {
                        $order = 0;
                    } else {
                        $order = 1;
                    }
                }
                $_SESSION['_order'] = $order;
                
                if($order == 0) {
                    $arrow = '&#9650;';
                } else {
                    $arrow = '&#9660;';
                }
            }

            $th .= $this->object([
                'tag'   => 'th',
                'content'   => $this->object([
                    'tag'   => 'a',
                    'param' => [
                        'class' => 'th',
                        'href'  => '?_orderby=' . $value . '&_csrf=' . $_SESSION['_csrf']
                    ],
                    'content'   => $key
                ]) . $arrow
            ]);
        }

        # Sort the data
        if ($orderby != '') {
            $data = record_sort($data,$orderby,$order);
        }

        if (count($actions) > 0) {
            $th .= $this->object([
                'tag'   => 'th',
                'content'   => 'Actions'
            ]);
        }

        $thead = $this->object([
            'tag'   => 'tr',
            'param' => [
                'class' => 'table-dark'
            ],
            'content'   => $th
        ]);

        $tbody = '';
        
        foreach ($data as $record) {
            $td = '';
            foreach ($head as $key => $value) {
                
                $td .= $this->object([
                    'tag'   => 'td',
                    'content'   => htmlentities(isset($record[$value]) ? $record[$value] : '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                ]);
            }

            // -- do the actions
            if (count($actions) > 0) {
                $ac = '';
                foreach ($actions as $act) {

                    $ac .= $this->object([
                        'tag'   => 'a',
                        'param' => [
                            'class' => [
                                'col-sm-3',
                                'btn',
                                'text-center',
                                isset($act['btn']) ? $act['btn'] : 'btn-primary',
                            ],
                            // TODO - make id a conditional and a variable
                            'href'  => $act['link'] . '&id=' . $record['id'] . '&_csrf=' . $_SESSION['_csrf']
                        ],
                        'content'   => $act['text']
                    ]);

                }
                $td .= $this->object([
                    'tag'   => 'td',
                    'content'   => $ac
                ]);
            }
            $tbody .= $this->object([
                'tag'   => 'tr',
                'content'   => $td
            ]);
        }

        $this->content(
            $this->object([
                'tag'   => 'div',
                'param' => [
                    'class' => [
                        'data-table',
                        'form-horizontal',
                    ],
                ],
                'content' => $this->object([
                    'tag'   => 'table',
                    'param' => [
                        'class' => [
                            'table',
                            'table-hover'
                        ]
                    ],
                    'content'   => $thead . $tbody
                ])
            ])
        );
    }

    function form($param,$content) {
        if(!isset($param['func'])) {
            $param['func'] = '';
        }

        if(isset($param['id']) && $param['id'] != '') {
            $id = $this->object([
                'tag'   => 'input',
                'param' => [
                    'type'  => 'hidden',
                    'name'  => 'id',
                    'value' => $param['id']
                ]
                ]);
        } else {
            $id = '';
        }

        $this->style('
            .data-form {
                width: 75%;
                margin: 50px auto;
                font-size: 15px;
            }
            .data-form form {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 30px;
            }
        ');

        // -- csrf
        $csrf = '';
        if(isset($_SESSION['_csrf'])) {
            $csrf = $this->object([
                'tag'   => 'input',
                'param' => [
                    'type'  => 'hidden',
                    'name'  => '_csrf',
                    'value' => $_SESSION['_csrf']
                ],
            ]);
        }

        $this->content(
            $this->object([
                'tag'   => 'div',
                'param' => [
                    'class' => [
                        'data-form',
                        'form-horizontal',
                    ],
                ],
                'content' => $this->object([
                    'tag'   => 'form',
                    'param' => [
                        'class' => [
                            'row',
                            'g-3'
                        ],
                        'action' => $param['action'],
                        'method'    => 'post'
                    ],
                    'content' => $content . 
                
                    $this->object([
                        'tag'   => 'div',
                        'param' => [
                            'class' => [
                                'input-group',
                                'md-4',
                                //'d-grid',
                                'gap-2'
                            ]
                        ],
                        'content'   => $this->object([
                            'tag'   => 'button',
                            'param' => [
                                'class' => [
                                    'btn',
                                    'btn-primary',
                                    'btn-block',
                                    'col-sm-8',
                                ],
                            ],
                            'content' => $param['submit']
                        ]) .
                        $this->object([
                            'tag'   => 'a',
                            'param' => [
                                'class' => [
                                    'col-sm-2',
                                    'btn',
                                    'btn-success'
                                ],
                                'href'  => '?_csrf=' . $_SESSION['_csrf']
                            ],
                            'content'   => 'Back'
                        ])
                    ])
                    
                    . $csrf
                    . $id
                    . $this->object([
                        'tag'   => 'input',
                        'param' => [
                            'type'  => 'hidden',
                            'name'  => '_func',
                            'value' => $param['func']
                        ],
                    ])
                ])  
            ])
        );


    }

    function form_field($name,$type,$desc,$required,$value,$options = [],$helptext = '') {
        $F = [
            'name' => $name,
            'type'  => $type,
            'required' => $required == 'True',
            'placeholder' => $desc,
            'value' => $value,
            'options' => $options
        ];
        return $this->object([
            'tag'   => 'div',
            'param' => [
                'class' => [
                    'input-group',
                    'mb-3'
                ],
            ],
            'content' => $this->object([
                'tag'       => 'label',
                'param'     => [
                    'class'     => [ 
                        'col-sm-2',
                        'col-form-label',
                        'fw-bold'
                    ],
                    'for'       => $name,
                ],
                'content'   => $desc
            ]) . 
            $this->object([
                'tag'       => 'div',
                'param'     => [
                    'class'     => 'col-sm-10'
                ],
                'content'   =>

                    $this->elements($F) .
                
                    ($helptext != '' ? 
                    $this->object([
                        'tag'   => 'small',
                        'param' => [
                            'id'    => $F['name'] . 'Help',
                            'class' => [
                                'form-text',
                                'text-muted'
                            ],
                        ],
                        'content'   => $helptext
                        ]
                    ) : '')
            
            ]) 
            
        ]);

    }

    function login_form($param) {

        if(!isset($param['rememberme'])) {
            $param['rememberme'] = False;
        }
        
        // https://www.tutorialrepublic.com/snippets/bootstrap/simple-login-form.php

        // -- create
        $create = '';
        if(isset($param['create'])) {
            $create = $this->object([
                'tag'   => 'p',
                'param' => [
                    'class' => 'text-center'
                ],
                'content' => $this->object([
                    'tag'   => 'a',
                    'param' => [
                        'href' => $param['create'][1]
                    ],
                    'content' => $param['create'][0]
                ])
            ]);
        }

        // -- forget
        $forget = '';
        if(isset($param['forget'])) {
            $forget = $this->object([
                'tag'   => 'a',
                'param' => [
                    'class' => 'float-end',
                    'href'  => $param['forget'][1]
                ],
                'content'   => $param['forget'][0]
            ]);
        }

        // -- remember
        $remember = '';
        if(isset($param['remember'])) {
            $remember = $this->object([
                'tag'   => 'label',
                'param' => [
                    'class' => [
                        'float-start',
                        'form-check-label',
                    ],
                ],
                'content' => $this->object([
                    'tag'   => 'input',
                    'param' => [
                        'name'  => $param['rememberme'],
                        'type'  => 'checkbox'
                    ]
                ]) . $param['remember']
            ]);
        }

        // -- title
        if(!isset($param['title'])) {
            $param['title'] = "Log in";
        }

        // -- submit
        if(!isset($param['submit'])) {
            $param['submit'] = "Log in";
        }

        // -- csrf
        $csrf = '';
        if(isset($_SESSION['_csrf'])) {
            $csrf = $this->object([
                'tag'   => 'input',
                'param' => [
                    'type'  => 'hidden',
                    'name'  => '_csrf',
                    'value' => $_SESSION['_csrf']
                ]
                ]);
            
        }

        $this->style('
            .login-form {
                width: 340px;
                margin: 50px auto;
                font-size: 15px;
            }
            .login-form form {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 30px;
            }
            .login-form h2 {
                margin: 0 0 15px;
            }
            .form-control, .btn {
                min-height: 38px;
                border-radius: 2px;
            }
            .btn {        
                font-size: 15px;
                font-weight: bold;
            }
        ');

        $this->content(
            $this->object([
                'tag'       => 'div',
                'param'     => [
                    'class' => 'login-form'
                ],
                'content'    => $this->object([
                    'tag' => 'form',
                    'param' => [
                        'action' => 'index.php',
                        'method'    => 'post',
                    ],
                    'content' => $this->object([
                        'tag'   => 'h2',
                        'param' => [
                            'class' => 'text-center'
                        ],
                        'content'   => $param['title']
                    ]) .

                    $this->object([
                        'tag'       => 'div',
                        'param'     => [
                            'class' => [
                                'input-group',
                                'mb-3'
                            ]
                        ],
                        'content'   => $this->object([
                            'tag'           => 'input',
                            'param' => [
                                'type'          => 'text',
                                'autocomplete'  => 'off',
                                'class'         => [
                                    'form-control',
                                    'is-invalid'
                                ],
                                'name'          => 'emailaddress',
                                'placeholder'   => 'Email Address'
                            ],
                            'required'  => True
                        ])
                    ]) .
                    
                    $this->object([
                        'tag'       => 'div',
                        'param'     => [
                            'class' => [
                                'input-group',
                                'mb-3'
                            ]
                        ],
                        'content'   => $this->object([
                            'tag'           => 'input',
                            'param' => [
                                'type'          => 'password',
                                'autocomplete'  => 'off',
                                'class'         => [
                                    'form-control',
                                    'is-invalid'
                                ],
                                'name'          => 'password'
                            ],
                            'required'  => True
                        ])
                    ])  .
                    
                    $this->object([
                        'tag'   => 'div',
                        'param' => [
                            'class' => [
                                'input-group',
                                'mb-3',
                                'd-grid',
                                'gap-2'
                            ]
                        ],
                        'content'   => $this->object([
							'tag' => 'button',
							'param' => [
								'type' => 'submit',
								'class' => [
									'btn',
									'btn-primary',
									'btn-block',
								],
							],
							'content' => $param['submit']
						])
                    ]) .

                    $this->object([
                        'tag'   => 'div',
                        'param' => [
                            'class' => "clearfix",
                        ],
                        'content'   => $remember . $forget
                    ]) .
                    $csrf
                ]) .
                $create
            ])
        );
    }

    function change_password_form($param) {
        
        // -- action
        if(!isset($param['action'])) {
            $param['action'] = "index.php";
        }

        // -- title
        if(!isset($param['title'])) {
            $param['title'] = "Change Password";
        }

        // -- submit
        if(!isset($param['submit'])) {
            $param['submit'] = "Change Password";
        }
        // -- func
        if(!isset($param['func'])) {
            $param['func'] = "change_password";
        }

        // -- csrf
        $csrf = '';
        if(isset($_SESSION['_csrf'])) {
            $csrf = $this->object([
                'tag'   => 'input',
                'param' => [
                    'type'  => 'hidden',
                    'name'  => '_csrf',
                    'value' => $_SESSION['_csrf']
                ]
                ]);
        }

        $this->style('
            .login-form {
                width: 340px;
                margin: 50px auto;
                font-size: 15px;
            }
            .login-form form {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 30px;
            }
            .login-form h2 {
                margin: 0 0 15px;
            }
            .form-control, .btn {
                min-height: 38px;
                border-radius: 2px;
            }
            .btn {        
                font-size: 15px;
                font-weight: bold;
            }
        ');

        $this->content(
            $this->object([
                'tag'       => 'div',
                'param'     => [
                    'class' => 'login-form'
                ],
                'content'    => $this->object([
                    'tag' => 'form',
                    'param' => [
                        'action' => $param['action'],
                        'method'    => 'post'
                    ],
                    'content' => $this->object([
                        'tag'   => 'h2',
                        'param' => [
                            'class' => 'text-center'
                        ],
                        'content'   => $param['title']
                    ]) .

                    $this->object([
                        'tag'       => 'div',
                        'param'     => [
                            'class' => [
                                'input-group',
                                'mb-3'
                            ]
                        ],
                        'content'   => $this->object([
                            'tag'           => 'input',
                            'param' => [
                                'type'          => 'password',
                                'autocomplete'  => 'off',
                                'class'         => [
                                    'form-control',
                                    'is-invalid'
                                ],
                                'name'          => 'password1',
                                'placeholder' => 'Enter a new password'
                            ],
                            'required'  => True
                        ])
                    ])  .
                    $this->object([
                        'tag'       => 'div',
                        'param'     => [
                            'class' => [
                                'input-group',
                                'mb-3'
                            ]
                        ],
                        'content'   => $this->object([
                            'tag'           => 'input',
                            'param' => [
                                'type'          => 'password',
                                'autocomplete'  => 'off',
                                'class'         => [
                                    'form-control',
                                    'is-invalid'
                                ],
                                'name'          => 'password2',
                                'placeholder' => 'Confirm Password'
                            ],
                            'required'  => True
                        ])
                    ])  .
                    
                    $this->object([
                        'tag'   => 'div',
                        'param' => [
                            'class' => [
                                'input-group',
                                'mb-3',
                                'd-grid',
                                'gap-2'
                            ]
                        ],
                        'content'   => $this->object([
							'tag' => 'button',
							'param' => [
								'type' => 'submit',
								'class' => [
									'btn',
									'btn-primary',
									'btn-block',
								],
							],
							'content' => $param['submit']
						])
                    ]) .
                    $csrf .
                    $this->object([
                        'tag'   => 'input',
                        'param' => [
                            'type'  => 'hidden',
                            'name'  => 'func',
                            'value' => $param['func']
                        ]
                        ])
                ]) 
            ])
        );
    }

    function render_menu($menu) {

        // build the menu structure
        $structure = '';

        foreach ($menu as $item => $link) {
            if(is_array($link)) {

                // -- produce the inside loop of the menu
                $inside = '';
                foreach ($link as $subitem => $sublink) {
                    $inside .= $this->object([
                        'tag'   => 'a',
                        'param' => [
                            'class' => 'dropdown-item',
                            'href'  => $sublink
                        ],
                        'content'   => $subitem
                    ]);
                }

                $structure .= $this->object([
                    'tag'       => 'li',
                    'param'     => [
                        'class' => [
                            'nav-item',
                            'dropdown'
                        ]
                    ],
                    'content'   => $this->object([
                        'tag'   => 'a',
                        'param' => [
                            'class' => [
                                'nav-link',
                                'dropdown-toggle'
                            ],
                            'href'              => '#',
                            'id'                => 'navbarDropdown',
                            'role'              => 'button',
                            'data-bs-toggle'    => 'dropdown',
                            'aria-haspopup'     => 'true',
                            'aria-expanded'     => 'false'
                        ],
                        'content'   => $item
                    ]) .
                    $this->object([
                        'tag'   => 'div',
                        'param' => [
                            'class' => "dropdown-menu"
                        ],
                        'content'   => $inside
                    ])
                ]);
            } else {
                $structure .= $this->object([
                    'tag'   => 'li',
                    'param' => [
                        'class' => 'nav-item'
                    ],
                    'content'   => $this->object([
                        'tag'   => 'a',
                        'param' => [
                            'class' => 'nav-link',
                            'href'  => $link
                        ],
                        'content'   => $item
                    ])
                ]);
            }
        }

        // merge it into the nav
        return $this->object([
            'tag'   => 'nav',
            'param' => [
                'class' => [
                    'navbar',
                    'navbar-expand-lg',
                    'navbar-dark',
                    'bg-dark',
                    // TODO - Fixing the top of the nav bar causes the rest of the text to be hidden behind it
                    //'fixed-top'
                ]
            ],
            'content'   => $this->object([
                'tag'   => 'div',
                'param' => [
                    'class' => 'container-fluid'
                ],
                'content'   => $this->object([
                    'tag'   => 'a',
                    'param' => [
                        'class' => 'navbar-brand',
                        'href'  => 'index.php'
                    ],
                    'content'   => $this->attributes['title']
                ]) .
                
                $this->object([
                    'tag'       => 'button',
                    'param'     => [
                        'class'             => 'navbar-toggler',
                        'type'              => 'button',
                        'data-bs-toggle'    => 'collapse',
                        'data-bs-target'    => '#navbarSupportedContent',
                        'aria-controls'     => 'navbarSupportedContent',
                        'aria-expanded'     => 'false',
                        'aria-label'        => 'Toggle navigation'
                    ],
    
                    'content'   => $this->object([
                        'tag'   => 'span',
                        'param' => [
                            'class' => 'navbar-toggler-icon'
                        ],
                        'content'   => ''
                    ])
                ])
                .
                $this->object([
                    'tag'       => 'div',
                    'param'     => [
                        'class' => [
                            'collapse',
                            'navbar-collapse'
                        ],
                        'id'    => 'navbarSupportedContent'
                    ],
                    'content'   => $this->object([
                        'tag'       => 'ul',
                        'param'     => [
                            'class' => [
                                'navbar-nav',
                                'me-auto',
                                'mb-2',
                                'mb-lg-0'
                            ]
                        ],
                        'content'   => $structure
                    ])  
                ])
            ])
        ]);
    }

    function h1($txt) {
        $this->content(
            $this->object([
                'tag'       => 'h1',
                'content'   => $txt
            ])
        );
    }

    function h2($txt) {
        $this->content(
            $this->object([
                'tag'       => 'h2',
                'content'   => $txt
            ])
        );
    }

    function tile($txt,$lnk) {
        $this->content(
            $this->object([
                'tag'   => 'a',
                'param' => [
                    'class' => [
                        'col-sm-2',
                        'btn',
                        'btn-lg',
                        'btn-secondary',
                        'p-4'
                    ],
                    'href'  => $lnk
                ],
                'content'   => $txt
            ])
        );
    }

    function button($txt,$lnk) {
        $this->content(
            $this->object([
                'tag'   => 'div',
                'param' => [
                    'class' => [
                        'col-sm',
                        //'align-items-right',
                        'text-right'
                    ]
                ],
                'content' => $this->object([
                    'tag'   => 'a',
                    'param' => [
                        'class' => [
                            'col-sm-2s',
                            'btn',
                            'btn-primary',
                            'text-center'
                        ],
                        'href'  => $lnk
                    ],
                    'content'   => $txt
                ])
            ])
        );
    }
}
?>