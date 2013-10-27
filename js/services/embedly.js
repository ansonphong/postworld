'use strict';

postworld.service('embedly', ['$log', function ($log) {
        return {
            liveEmbedlyExtract: function( link_url ){
                // LIVE EMBEDLY EXTRACT
                // API : http://embed.ly/docs/extract/api

                //return: embedly_extract_obj;
            },
            translateToPostData: function( embedly_extract ){
                if (typeof embedly_extract.images[0] !== 'undefined' )
                    var link_url_set = embedly_extract.images[0].url;
                else
                    var link_url_set = ""; // defult image_url

                return{
                    post_title: embedly_extract.title,
                    post_excerpt: embedly_extract.description,
                    link_url: embedly_extract.url,
                    image_url: link_url_set,
                };
            },
            embedlyExtractImageMeta: function( embedly_extract ){
                
                if ( embedly_extract.images.length >= 1 )
                    var image_status_set = true;
                else
                    var image_status_set = false;

                return{
                    image_status: image_status_set,
                    image_count: embedly_extract.images.length,
                    images: embedly_extract.images,
                };
            },
            embedly_extract: function(){
                var embedly_extract_multi_object = [
                    {   // YOU TUBE
                        "provider_url": "http://www.youtube.com/", 
                        "authors": [], 
                        "provider_display": "www.youtube.com", 
                        "related": [], 
                        "favicon_url": "http://s.ytimg.com/yts/img/favicon-vfldLzJxy.ico", 
                        "keywords": [
                            {
                                "score": 32, 
                                "name": "google"
                            }, 
                            {
                                "score": 30, 
                                "name": "picasa"
                            }, 
                            {
                                "score": 30, 
                                "name": "orkut"
                            }, 
                            {
                                "score": 30, 
                                "name": "mavireck"
                            }, 
                            {
                                "score": 26, 
                                "name": "chrome"
                            }, 
                            {
                                "score": 26, 
                                "name": "nova"
                            }, 
                            {
                                "score": 20, 
                                "name": "earth"
                            }, 
                            {
                                "score": 20, 
                                "name": "gmail"
                            }, 
                            {
                                "score": 17, 
                                "name": "video"
                            }, 
                            {
                                "score": 16, 
                                "name": "youtube"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "http://www.youtube.com/watch?v=38peWm76l-U", 
                        "media": {
                            "duration": 6862, 
                            "width": 500, 
                            "html": "<iframe width=\"500\" height=\"281\" src=\"http://www.youtube.com/embed/38peWm76l-U?feature=oembed\" frameborder=\"0\" allowfullscreen></iframe>", 
                            "type": "video", 
                            "height": 281
                        }, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 3, 
                                "name": "Google"
                            }, 
                            {
                                "count": 3, 
                                "name": "Picasa"
                            }, 
                            {
                                "count": 3, 
                                "name": "Google Account"
                            }, 
                            {
                                "count": 1, 
                                "name": "PBS NOVA Finding Life Beyond Earth 2011 Legendado \\*\\* Beatiful Nature Around The World"
                            }, 
                            {
                                "count": 1, 
                                "name": "NASA"
                            }, 
                            {
                                "count": 1, 
                                "name": "Earth"
                            }
                        ], 
                        "provider_name": "YouTube", 
                        "type": "html", 
                        "description": "The groundbreaking two-hour special that reveals a spectacular new space-based vision of our planet. Produced in extensive consultation with NASA scientists, NOVA takes data from earth-observing satellites and transforms it into dazzling visual sequences, each one exposing the intricate and surprising web of forces that sustains life on earth.", 
                        "embeds": [], 
                        "images": [
                            {
                                "width": 480, 
                                "url": "http://i1.ytimg.com/vi/38peWm76l-U/hqdefault.jpg", 
                                "height": 360, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            45, 
                                            70, 
                                            75
                                        ], 
                                        "weight": 0.279296875
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            2, 
                                            4
                                        ], 
                                        "weight": 0.25537109375
                                    }, 
                                    {
                                        "color": [
                                            84, 
                                            104, 
                                            111
                                        ], 
                                        "weight": 0.16455078125
                                    }, 
                                    {
                                        "color": [
                                            111, 
                                            136, 
                                            149
                                        ], 
                                        "weight": 0.1259765625
                                    }, 
                                    {
                                        "color": [
                                            144, 
                                            173, 
                                            190
                                        ], 
                                        "weight": 0.118896484375
                                    }
                                ], 
                                "entropy": 5.90822075866, 
                                "size": 44269
                            }, 
                            {
                                "width": 48, 
                                "url": "https://lh5.googleusercontent.com/-LBcQruSPiVE/AAAAAAAAAAI/AAAAAAAAAAA/ZZy901nR234/s48-c-k/photo.jpg", 
                                "height": 48, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            172, 
                                            188, 
                                            206
                                        ], 
                                        "weight": 0.162353515625
                                    }, 
                                    {
                                        "color": [
                                            113, 
                                            146, 
                                            179
                                        ], 
                                        "weight": 0.141357421875
                                    }, 
                                    {
                                        "color": [
                                            55, 
                                            102, 
                                            146
                                        ], 
                                        "weight": 0.10546875
                                    }, 
                                    {
                                        "color": [
                                            53, 
                                            79, 
                                            82
                                        ], 
                                        "weight": 0.09716796875
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            44, 
                                            114
                                        ], 
                                        "weight": 0.05615234375
                                    }
                                ], 
                                "entropy": 6.59194584276, 
                                "size": 2316
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "English", 
                        "url": "http://www.youtube.com/watch?v=38peWm76l-U", 
                        "title": "Earth From Space HD 1080p / Nova", 
                        "published": null

                    },
                    {   // SOUND CLOUD
                        "provider_url": "http://soundcloud.com", 
                        "authors": [], 
                        "provider_display": "soundcloud.com", 
                        "related": [], 
                        "favicon_url": "http://a1.sndcdn.com/favicon.ico?3eddc42", 
                        "keywords": [
                            {
                                "score": 11, 
                                "name": "log"
                            }, 
                            {
                                "score": 11, 
                                "name": "privacy"
                            }, 
                            {
                                "score": 10, 
                                "name": "2007-2013"
                            }, 
                            {
                                "score": 10, 
                                "name": "dmt"
                            }, 
                            {
                                "score": 10, 
                                "name": "trinkly"
                            }, 
                            {
                                "score": 10, 
                                "name": "soundcloud"
                            }, 
                            {
                                "score": 9, 
                                "name": "ambient"
                            }, 
                            {
                                "score": 8, 
                                "name": "lucid"
                            }, 
                            {
                                "score": 8, 
                                "name": "whirlpool"
                            }, 
                            {
                                "score": 6, 
                                "name": "imprint"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "https://soundcloud.com/bluetech/lost-found", 
                        "media": {
                            "width": 500, 
                            "html": "<iframe width=\"500\" height=\"166\" scrolling=\"no\" frameborder=\"no\" src=\"https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F116037149&show_artwork=true&maxwidth=900\"></iframe>", 
                            "type": "rich", 
                            "height": 166
                        }, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 1, 
                                "name": "SoundCloud Ltd."
                            }
                        ], 
                        "provider_name": "SoundCloud", 
                        "type": "html", 
                        "description": "Track #4 off of my new ambient album created for Lucid Dreaming practice.", 
                        "embeds": [], 
                        "images": [
                            {
                                "width": 500, 
                                "url": "http://i1.sndcdn.com/artworks-000060496011-f1jim7-t500x500.jpg?3eddc42", 
                                "height": 500, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            234, 
                                            240, 
                                            245
                                        ], 
                                        "weight": 0.352783203125
                                    }, 
                                    {
                                        "color": [
                                            198, 
                                            181, 
                                            207
                                        ], 
                                        "weight": 0.18896484375
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            125, 
                                            171
                                        ], 
                                        "weight": 0.168212890625
                                    }, 
                                    {
                                        "color": [
                                            71, 
                                            190, 
                                            229
                                        ], 
                                        "weight": 0.129638671875
                                    }, 
                                    {
                                        "color": [
                                            18, 
                                            12, 
                                            29
                                        ], 
                                        "weight": 0.072509765625
                                    }
                                ], 
                                "entropy": 5.81182154176, 
                                "size": 102652
                            }, 
                            {
                                "width": 400, 
                                "url": "http://i1.sndcdn.com/artworks-000060496011-f1jim7-crop.jpg?3eddc42", 
                                "height": 400, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            234, 
                                            240, 
                                            245
                                        ], 
                                        "weight": 0.35986328125
                                    }, 
                                    {
                                        "color": [
                                            197, 
                                            182, 
                                            208
                                        ], 
                                        "weight": 0.196044921875
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            126, 
                                            172
                                        ], 
                                        "weight": 0.160888671875
                                    }, 
                                    {
                                        "color": [
                                            74, 
                                            192, 
                                            231
                                        ], 
                                        "weight": 0.120849609375
                                    }, 
                                    {
                                        "color": [
                                            19, 
                                            14, 
                                            31
                                        ], 
                                        "weight": 0.070556640625
                                    }

                                ],
                                "entropy": 5.755225306848601, 
                                "size": 73327
                            }, 
                            {
                                "width": 47, 
                                "url": "http://i1.sndcdn.com/avatars-000039409754-3pj42q-badge.jpg?3eddc42", 
                                "height": 47, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            71, 
                                            68, 
                                            65
                                        ], 
                                        "weight": 0.156982421875
                                    }, 
                                    {
                                        "color": [
                                            26, 
                                            24, 
                                            27
                                        ], 
                                        "weight": 0.138427734375
                                    }, 
                                    {
                                        "color": [
                                            105, 
                                            103, 
                                            98
                                        ], 
                                        "weight": 0.085693359375
                                    }, 
                                    {
                                        "color": [
                                            137, 
                                            132, 
                                            133
                                        ], 
                                        "weight": 0.076904296875
                                    }, 
                                    {
                                        "color": [
                                            178, 
                                            171, 
                                            173
                                        ], 
                                        "weight": 0.049072265625
                                    }
                                ], 
                                "entropy": 6.531873462937484, 
                                "size": 1756
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "English", 
                        "url": "http://soundcloud.com/bluetech/lost-found", 
                        "title": "Lost & Found by Bluetech", 
                        "published": null
                    },
                    {   // WEBSITE
                        "provider_url": "http://partners.nytimes.com", 
                        "authors": [], 
                        "provider_display": "partners.nytimes.com", 
                        "related": [], 
                        "favicon_url": null, 
                        "keywords": [
                            {
                                "score": 130, 
                                "name": "planetariums"
                            }, 
                            {
                                "score": 70, 
                                "name": "museum"
                            }, 
                            {
                                "score": 67, 
                                "name": "2000"
                            }, 
                            {
                                "score": 48, 
                                "name": "meteorite"
                            }, 
                            {
                                "score": 44, 
                                "name": "february"
                            }, 
                            {
                                "score": 37, 
                                "name": "center"
                            }, 
                            {
                                "score": 30, 
                                "name": "rose"
                            }, 
                            {
                                "score": 30, 
                                "name": "universe"
                            }, 
                            {
                                "score": 28, 
                                "name": "willamette"
                            }, 
                            {
                                "score": 27, 
                                "name": "space"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "http://partners.nytimes.com/library/national/science/planetarium-index.html", 
                        "media": {}, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 6, 
                                "name": "Earth"
                            }, 
                            {
                                "count": 5, 
                                "name": "American Museum of Natural History"
                            }, 
                            {
                                "count": 3, 
                                "name": "Willamette Meteorite"
                            }, 
                            {
                                "count": 2, 
                                "name": "Oregon"
                            }, 
                            {
                                "count": 2, 
                                "name": "GLENN COLLINS"
                            }, 
                            {
                                "count": 2, 
                                "name": "Hayden Planetarium"
                            }, 
                            {
                                "count": 1, 
                                "name": "Copernicus"
                            }, 
                            {
                                "count": 1, 
                                "name": "JAMES GLANZ"
                            }, 
                            {
                                "count": 1, 
                                "name": "Dr. Neil de Grasse Tyson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Ralph Appelbaum"
                            }, 
                            {
                                "count": 1, 
                                "name": "JULIE V. IOVINE"
                            }, 
                            {
                                "count": 1, 
                                "name": "NYC"
                            }, 
                            {
                                "count": 1, 
                                "name": "Manhattan"
                            }, 
                            {
                                "count": 1, 
                                "name": "ROBERT D. McFADDEN"
                            }, 
                            {
                                "count": 1, 
                                "name": "DAVID W. DUNLAP"
                            }, 
                            {
                                "count": 1, 
                                "name": "RITA REIF"
                            }, 
                            {
                                "count": 1, 
                                "name": "New York Convention and Visitors Bureau"
                            }, 
                            {
                                "count": 1, 
                                "name": "HERBERT MUSCHAMP"
                            }, 
                            {
                                "count": 1, 
                                "name": "Pluto"
                            }, 
                            {
                                "count": 1, 
                                "name": "Galileo"
                            }, 
                            {
                                "count": 1, 
                                "name": "JOHN NOBLE WILFORD"
                            }, 
                            {
                                "count": 1, 
                                "name": "MALCOLM W. BROWNE"
                            }, 
                            {
                                "count": 1, 
                                "name": "KENNETH CHANG"
                            }, 
                            {
                                "count": 1, 
                                "name": "JOHN SULLIVAN"
                            }, 
                            {
                                "count": 1, 
                                "name": "BENJAMIN WEISER"
                            }, 
                            {
                                "count": 1, 
                                "name": "SARAH BOXER"
                            }, 
                            {
                                "count": 1, 
                                "name": "MATTHEW MIRAPAUL"
                            }, 
                            {
                                "count": 1, 
                                "name": "EDWARD ROTHSTEIN"
                            }, 
                            {
                                "count": 1, 
                                "name": "Planetarium"
                            }, 
                            {
                                "count": 1, 
                                "name": "New York"
                            }, 
                            {
                                "count": 1, 
                                "name": "TINA KELLEY"
                            }, 
                            {
                                "count": 1, 
                                "name": "Cristyne F. Lategano"
                            }
                        ], 
                        "provider_name": "Nytimes", 
                        "type": "html", 
                        "description": "For Some, the Universe Is Over Their Heads By TINA KELLEY (March 7, 2000) Since the Rose Center for Earth and Space opened last month amid praise for its architecture and multidimensional tour of the universe, visitors have lined up with enthusiasm. And, sometimes, they have left in confusion.", 
                        "embeds": [], 
                        "images": [
                            {
                                "url": "http://graphics.nytimes.com/library/national/science/planetarium-index.jpg", 
                                "width": 305, 
                                "height": 256, 
                                "caption": null, 
                                "size": 29893
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.5.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 5606
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.2.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 5359
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.1.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 5191
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.3.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 4344
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "English", 
                        "url": "http://partners.nytimes.com/library/national/science/planetarium-index.html", 
                        "title": "Space: Rose Center for Earth and Space", 
                        "published": null
                    },
                    {   // VIMEO
                        "provider_url": "https://vimeo.com/", 
                        "authors": [], 
                        "provider_display": "vimeo.com", 
                        "related": [], 
                        "favicon_url": "http://a.vimeocdn.com/images_v6/favicon_32.ico", 
                        "keywords": [
                            {
                                "score": 39, 
                                "name": "patterns"
                            }, 
                            {
                                "score": 38, 
                                "name": "networks"
                            }, 
                            {
                                "score": 25, 
                                "name": "systems"
                            }, 
                            {
                                "score": 20, 
                                "name": "recurring"
                            }, 
                            {
                                "score": 19, 
                                "name": "ecosystems"
                            }, 
                            {
                                "score": 18, 
                                "name": "vimeo"
                            }, 
                            {
                                "score": 18, 
                                "name": "cells"
                            }, 
                            {
                                "score": 18, 
                                "name": "whitworth"
                            }, 
                            {
                                "score": 17, 
                                "name": "slime"
                            }, 
                            {
                                "score": 16, 
                                "name": "creative"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "http://vimeo.com/34182381", 
                        "media": {
                            "duration": 105, 
                            "width": 500, 
                            "html": "<iframe src=\"http://player.vimeo.com/video/34182381\" width=\"500\" height=\"281\" frameborder=\"0\" title=\"TO UNDERSTAND IS TO PERCEIVE PATTERNS\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>", 
                            "type": "video", 
                            "height": 281
                        }, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 2, 
                                "name": "Rob Whitworth"
                            }, 
                            {
                                "count": 1, 
                                "name": "Cheryl Colan"
                            }, 
                            {
                                "count": 1, 
                                "name": "Pedro Miguel Cruz"
                            }, 
                            {
                                "count": 1, 
                                "name": "Moon Soundtrack"
                            }, 
                            {
                                "count": 1, 
                                "name": "Johnson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Aaron Koblin"
                            }, 
                            {
                                "count": 1, 
                                "name": "Tiffany Shlain"
                            }, 
                            {
                                "count": 1, 
                                "name": "Adrian Bejan"
                            }, 
                            {
                                "count": 1, 
                                "name": "Takuya Hosogane"
                            }, 
                            {
                                "count": 1, 
                                "name": "Stephen Johnson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Katie Armstrong"
                            }, 
                            {
                                "count": 1, 
                                "name": "Manhattan"
                            }, 
                            {
                                "count": 1, 
                                "name": "Jared Raab"
                            }, 
                            {
                                "count": 1, 
                                "name": "Andrea Tseng"
                            }, 
                            {
                                "count": 1, 
                                "name": "Steven Johnson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Angela Palmer"
                            }, 
                            {
                                "count": 1, 
                                "name": "Clint Mansell"
                            }, 
                            {
                                "count": 1, 
                                "name": "Paul Stammetts"
                            }, 
                            {
                                "count": 1, 
                                "name": "Jason Silva"
                            }, 
                            {
                                "count": 1, 
                                "name": "Jesse Kanda"
                            }, 
                            {
                                "count": 1, 
                                "name": "Death & Technology"
                            }
                        ], 
                        "provider_name": "Vimeo", 
                        "type": "html", 
                        "description": "Follow me on Twitter: https://twitter.com/JasonSilva @JasonSilva and @notthisbody Special thanks to filmmaker/photographer Rob Whitworth for allowing a clip from his video (https://vimeo.com/32958521) to be featured. Check out his website: www.robwhitworth.co.uk My videos: Beginning of Infinity - http://vimeo.com/29938326 Imagination - http://vimeo.com/34902950 INSPIRATION: The Imaginary Foundation says \"To Understand Is To Perceive Patterns\"...", 
                        "embeds": [], 
                        "images": [
                            {
                                "width": 1280, 
                                "url": "http://b.vimeocdn.com/ts/232/668/232668361_1280.jpg", 
                                "height": 740, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            2, 
                                            6, 
                                            13
                                        ], 
                                        "weight": 0.478515625
                                    }, 
                                    {
                                        "color": [
                                            39, 
                                            41, 
                                            36
                                        ], 
                                        "weight": 0.2099609375
                                    }, 
                                    {
                                        "color": [
                                            192, 
                                            157, 
                                            115
                                        ], 
                                        "weight": 0.109130859375
                                    }, 
                                    {
                                        "color": [
                                            76, 
                                            26, 
                                            76
                                        ], 
                                        "weight": 0.1064453125
                                    }, 
                                    {
                                        "color": [
                                            151, 
                                            59, 
                                            101
                                        ], 
                                        "weight": 0.03759765625
                                    }
                                ], 
                                "entropy": 5.29376026059, 
                                "size": 234555
                            }, 
                            {
                                "width": 75, 
                                "url": "http://b.vimeocdn.com/ps/323/365/3233659_75.jpg", 
                                "height": 75, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            19, 
                                            19, 
                                            19
                                        ], 
                                        "weight": 0.596923828125
                                    }, 
                                    {
                                        "color": [
                                            207, 
                                            207, 
                                            207
                                        ], 
                                        "weight": 0.301025390625
                                    }, 
                                    {
                                        "color": [
                                            138, 
                                            138, 
                                            138
                                        ], 
                                        "weight": 0.10205078125
                                    }
                                ], 
                                "entropy": 3.42717878362, 
                                "size": 8744
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "Lithuanian", 
                        "url": "http://vimeo.com/34182381", 
                        "title": "TO UNDERSTAND IS TO PERCEIVE PATTERNS", 
                        "published": null
                    }

                ];

                //return embedly_extract_multi_object[0];
                
                // Return Random
                return embedly_extract_multi_object[Math.floor(Math.random() * embedly_extract_multi_object.length)];

            },
            
        };
    }]);
    
postworld.controller('pwEmbedly', ['embedly2',     function pwEmbedly($scope, $location, $log, pwData, $attrs, embedly2) {
	    //$scope.oEmbedDecode = $sce.trustAsHtml( $scope.oEmbedDecode );
	    //$scope.oEmbed = "";
	    $scope.embedlyGet = function (link_url) {
	    	console.log('embedly');
	    	
	        var args = { "link_url":link_url };
	        var oEmbed = "";
	        pwData.wp_ajax('ajax_oembed_get', args ).then(
	            // Success
	            function(response) {    
	                $scope.oEmbed = $sce.trustAsHtml(response.data);
	            },
	            // Failure
	            function(response) {
	                alert("error");
	            }
	        );
	              
	    };    	
    }
]
);
