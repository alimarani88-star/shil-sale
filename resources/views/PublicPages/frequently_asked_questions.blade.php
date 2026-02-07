@extends('Customer.Layout.master')
@section('title', 'سوالات متداول | شیل ایران')

@section('meta')
    <meta name="description"
          content="پاسخ به سوالات متداول درباره محصولات و خدمات شرکت شیل ایران">
    <meta name="keywords"
          content="سوالات متداول, پرسش و پاسخ, شیل ایران, FAQ">
    <link rel="canonical" href="{{ url()->current() }}"/>
@endsection

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container-Pm {
            min-height: 100vh;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }

        .container-Pm::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background:
                radial-gradient(circle at 20% 30%, rgba(138, 43, 226, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(147, 51, 234, 0.15) 0%, transparent 50%); */
            pointer-events: none;
        }

        .container-pm {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 80px;
        }

        .header-icon {
            font-size: 4em;
            margin-bottom: 20px;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .header h1 {
            color: #fff;
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 16px;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.3em;
            font-weight: 300;
        }


        .category-card {
            background: linear-gradient(135deg, #0a0015 0%, #1a0b2e 50%, #2d1b4e 100%);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .category-card:hover::before {
            left: 100%;
        }

        .category-card:hover {
            border-color: rgba(168, 85, 247, 0.6);
            box-shadow: 0 20px 60px rgba(168, 85, 247, 0.3);
            transform: translateY(-8px);
        }

        .category-card.active {
            border-color: #a855f7;
            box-shadow: 0 20px 60px rgba(168, 85, 247, 0.4);
        }

        .category-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(236, 72, 153, 0.2));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            color: #fff;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }

        .category-card:hover .category-icon {
            transform: scale(1.1) rotate(-5deg);
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.3), rgba(236, 72, 153, 0.3));
        }

        .category-title {
            color: #fff;
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .category-count {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9em;
        }

        .faq-container {
            display: grid;
            gap: 20px;
        }

        

     

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .faq-item:hover {
            border-color: rgba(168, 85, 247, 0.5);
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.2);
        }

        .faq-question {
            padding: 25px 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            transition: all 0.3s ease;
            position: relative;
        }

        .faq-question:hover {
            background: rgba(168, 85, 247, 0.05);
        }

        .faq-item.active .faq-question {
            background: rgba(168, 85, 247, 0.1);
        }

        .question-content {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

       

        .question-text {
            color: #fff;
            font-size: 1.1em;
            font-weight: 500;
        }

        

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 30px 25px 30px;
        }

        .answer-content {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            font-size: 1.02em;
            padding-right: 55px;
        }

        .search-box {
            max-width: 600px;
            margin: 0 auto 60px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 18px 60px 18px 25px;
            background: linear-gradient(135deg, #0a0015 0%, #1a0b2e 50%, #2d1b4e 100%);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 50px;
            color: #fff;
            font-size: 1.05em;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-input:focus {
            border-color: #a855f7;
            box-shadow: 0 0 30px rgba(168, 85, 247, 0.3);
        }

        .search-icon {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: #a855f7;
            font-size: 1.3em;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5em;
            }

            .header-icon {
                font-size: 3em;
            }


            .faq-question {
                padding: 20px;
            }

            .question-text {
                font-size: 1em;
            }

            .answer-content {
                padding-right: 0;
                font-size: 0.95em;
            }

            .faq-item.active .faq-answer {
                padding: 0 20px 20px 20px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 2em;
            }

            .category-icon {
                width: 60px;
                height: 60px;
                font-size: 1.8em;
            }

            .category-title {
                font-size: 1.1em;
            }
        }
    </style>

    <div class="container-Pm">
        <div class="container-pm">
            <div class="header">
                <div class="header-icon custom-primary">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h1 class="custom-primary">سوالات متداول</h1>
                <p class="custom-cl-primary">پاسخ سوالات خود را اینجا پیدا کنید</p>
            </div>


            <div class="faq-container" id="faqContainer">
                <!-- سوالات سفارش و خرید -->
                 @foreach ($questions_answers as $key => $item)
                       <div class="faq-item" data-category="order">
                    <div class="faq-question">
                        <div class="question-content">
                            <div class="question-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="question-text">{{$key}}</div>
                        </div>
                        <div class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">{{ $item }}</div>
                    </div>
                </div>
                 @endforeach

            </div>
        </div>
    </div>

    <script>
        // تابع برای باز و بسته کردن سوالات
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const wasActive = faqItem.classList.contains('active');

                // بستن همه سوالات
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });

                // اگر سوال فعال نبود، باز کن
                if (!wasActive) {
                    faqItem.classList.add('active');
                }
            });
        });

        // فیلتر کردن بر اساس دسته‌بندی
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', () => {
                const category = card.getAttribute('data-category');

                // تغییر کلاس active
                document.querySelectorAll('.category-card').forEach(c => {
                    c.classList.remove('active');
                });
                card.classList.add('active');

                // نمایش سوالات مربوط به دسته
                document.querySelectorAll('.faq-item').forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('active');
                    }
                });
            });
        });

        // جستجو در سوالات
        document.querySelector('.search-input').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();

            document.querySelectorAll('.faq-item').forEach(item => {
                const questionText = item.querySelector('.question-text').textContent.toLowerCase();
                const answerText = item.querySelector('.answer-content').textContent.toLowerCase();

                if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
@endsection
