/* IP V4*/
struct sockaddr_in {
sa_family_t    sin_family; /* address family: AF_INET */
in_port_t      sin_port;   /* port in network byte order */
struct in_addr sin_addr;   /* internet address */
};
       
/* Internet address. */
struct in_addr {
uint32_t       s_addr;     /* address in network byte order */
};
    
/* IP V6*/
struct sockaddr_in6 { 
sa_family_t     sin6_family;   /* AF_INET6 */ 
in_port_t       sin6_port;     /* port number */ 
uint32_t        sin6_flowinfo; /* IPv6 flow information */ 
struct in6_addr sin6_addr;     /* IPv6 address */ 
uint32_t        sin6_scope_id; /* Scope ID (new in 2.4) */ 
};

/* Internet address. */
struct in6_addr { 
unsigned char   s6_addr[16];   /* IPv6 address */ 
};


/*
*	Function Name:	SOCKET
*INPUT
*	protofamily:	协议域/协议族. AF_INET(IPV4)、AF_INET6(IPV6), AF_LOCAL(aka.AF_UNIX，Unix socket), AF_ROUTE and so on.
*					It defines the type of socket address. E.g, AF_INET means IPV4(32bits) + Port(16bits).
*	tye:			Type of socket. E.g, SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_PACKET, SOCK_SEQPACKET.
*	protocol:		E.g, IPPROTO_TCP、IPPTOTO_UDP、IPPROTO_SCTP、IPPROTO_TIPC. 
*				TCP/UDP区分
*OUTPUT
*	return:			sockfd
*	
*/
int  socket(int protofamily, int type, int protocol);


/*
*	Function Name:	BIND
*INPUT
*	sockfd:		Describle word of socket. It was generated from socket and is identical id for unique socket connection.
*			它是通过socket()函数创建，唯一标识一个socket.
*	addr:		Pointer of const struct sockaddr *. Points to the address of protocol that attached to sockfd.
*/
int bind(int sockfd, const struct sockaddr *addr, socklen_t addrlen);


/*
*	Function Name:	LISTEN. 
*			Listen from Server. 作为服务器，在socket, bind之后通过listen监听先前的socket就能收到connect过来的请求
*			Listen makes socket passive mode. (Active mode for default)
*INPUT
*	sockfd:		Defines which socket.
*	backlog:	Maximum waitting request for socket.
*/
int listen(int sockfd, int backlog);


/*
*	Function Name:	CONNECT
*			Request from client. 客户端请求
*			Connect keeps socket active mode.
*INPUT 
*	sockfd:		Defines which socket.
*	addr:		Address of Server socket
*	addrlen:	Length of socket address
*/
int connect(int sockfd, const struct sockaddr *addr, socklen_t addrlen);


/*
*	Function Name:	ACCEPT
*
*INPUT
*	sockfd:		Defines which socket.
*	addr:		Result Parameter! It returns client address. If you are net interested in it, set it NULL.
*	addrlen:	Result Parameter! It returns the length of client address. Samely, you can set it NULL.
*OUTPUT
*	return:		connect_fd of this connection
*NOTICE
*	ACCEPT will block process. Once accepted it will return connect_fd, please distinguish connect_fd from socketfd.
*	Nomarlly, there is only one socketfd in server, but more than one connect_fd.
*/
int accept(int sockfd, struct sockaddr *addr, socklen_t *addrlen);


/*
*	I/O  Functions
*/
#include <unistd.h>
ssize_t read(int fd, void *buf, size_t count);
ssize_t write(int fd, const void *buf, size_t count);

#include <sys/types.h>
#include <sys/socket.h>
ssize_t send(int sockfd, const void *buf, size_t len, int flags);
ssize_t recv(int sockfd, void *buf, size_t len, int flags);
ssize_t sendto(int sockfd, const void *buf, size_t len, int flags, const struct sockaddr *dest_addr, socklen_t addrlen);
ssize_t recvfrom(int sockfd, void *buf, size_t len, int flags, struct sockaddr *src_addr, socklen_t *addrlen);
ssize_t sendmsg(int sockfd, const struct msghdr *msg, int flags);
ssize_t recvmsg(int sockfd, struct msghdr *msg, int flags);

/*
*	Function Name:	CLOSE
*/
#include <unistd.h>
int close(int fd);

/*
*	Three-way Handshake
*/
First:	建立连接时，客户端发送syn包(syn=j)到服务器，并进入SYN_SEND状态，等待服务器确认；SYN：同步序列编号(Synchronize Sequence Numbers)。
Second:	服务器收到syn包，必须确认客户的SYN（ack=j+1），同时自己也发送一个SYN包（syn=k），即SYN+ACK包，此时服务器进入SYN_RECV状态；
Third:	客户端收到服务器的SYN+ACK包，向服务器发送确认包ACK(ack=k+1)，此包发送完毕，客户端和服务器进入ESTABLISHED状态，完成三次握手。

