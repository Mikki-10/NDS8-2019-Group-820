///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

#ifndef NDS_LOGGER_H
#define NDS_LOGGER_H

#include "../header.hpp"
#include <netinet/in.h>
#include <nlohmann/json.hpp> /// https://github.com/nlohmann/json

using json = nlohmann::json;

#define BUFFER_SIZE 1024

typedef struct {

    struct hostent *host;
    struct sockaddr_in host_addr;
    String hostname;
    int port;                       /// port 9500 for our logging
    bool opened;                    /// Indicates whether the socket has been initialized
    int socketfd;                   /// socket file descriptor

} Connection;


class Logger {
private:

    String m_user;                  /// name of the remote supporter as username
    String m_system;                /// name of the current system (jumpserver or a critical system?)
    Connection m_conn;              /// connection structure for maintaining the sending operations
    String m_messageContainer;      /// string container for filling and sending the logs

    void connect();
    void disconnect();
    bool filter(char character);            /// decides whether to add character to logging variable or not
    void except(const String& message);     /// exception handler

public:
    Logger();
    ~Logger();
    void setLogin(const String& user);
    void setSystem(const String& system);
    void send(const String& content);
    void addCharToLog(char character);

};


#endif //NDS_LOGGER_H
