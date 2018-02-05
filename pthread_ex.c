/*Pthread Struct*/
typedef struct
{
    int                   etachstate;      //线程的分离状态
    int                   schedpolicy;     //线程调度策略
    structsched_param     schedparam;      //线程的调度参数
    int                   inheritsched;    //线程的继承性
    int                   scope;           //线程的作用域
    size_t                guardsize;       //线程栈末尾的警戒缓冲区大小
    int                   stackaddr_set;   //线程的栈设置
    void*                 stackaddr;       //线程栈的位置
    size_t                stacksize;       //线程栈的大小
}pthread_attr_t;

/*Pthread Operations*/
pthread_t							//线程ID
pthread_attr_t						//线程属性
pthread_create()					//创建一个线程
pthread_exit()						//终止当前线程
pthread_cancel()					//中断另外一个线程的运行
pthread_join()						//阻塞当前的线程，直到另外一个线程运行结束
pthread_attr_init()					//初始化线程的属性
pthread_attr_setdetachstate()		//设置脱离状态的属性（决定这个线程在终止时是否可以被结合）
pthread_attr_getdetachstate()		//获取脱离状态的属性
pthread_attr_destroy()				//删除线程的属性
pthread_kill()						//向线程发送一个信号
pthread_mutex_init()				//初始化互斥锁
pthread_mutex_destroy()				//删除互斥锁
pthread_mutex_lock()				//占有互斥锁（阻塞操作）
pthread_mutex_trylock()				//试图占有互斥锁（不阻塞操作）。即，当互斥锁空闲时，将占有该锁；否则，立即返回。
pthread_mutex_unlock()				//释放互斥锁
pthread_cond_init()					//初始化条件变量
pthread_cond_destroy()				//销毁条件变量
pthread_cond_signal()				//唤醒第一个调用pthread_cond_wait()而进入睡眠的线程
pthread_cond_wait()					//等待条件变量的特殊条件发生
Thread-local storage				//或者以Pthreads术语，称作线程特有数据）：
pthread_key_create()				//分配用于标识进程中线程特定数据的键
pthread_setspecific()				//为指定线程特定数据键设置线程特定绑定
pthread_getspecific()				//获取调用线程的键绑定，并将该绑定存储在 value 指向的位置中
pthread_key_delete()				//销毁现有线程特定数据键
pthread_attr_getschedparam()		//获取线程优先级
pthread_attr_setschedparam()		//设置线程优先级
pthread_equal()						//对两个线程的线程标识号进行比较
pthread_detach()					//分离线程

/*		TIPS
*		1. 线程创建的时候是默认joinable的，如果运行结束都没有被join就会发生2.的情况像zombie一样。
*		2. 线程创建运行结束后处于zombie状态还有资源不会自动释放，在苦苦等待父亲处理它。父进程可以调用pthread_detach(tid);回收它，
*		或者它自己绝望了可以调用pthread_detach(pthread_self())结束自己。
*/
