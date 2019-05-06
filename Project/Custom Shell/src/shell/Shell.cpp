//
// Custom Shell [ Pierre & Andrej ]
//

#include "Shell.h"


Shell::Shell() {

    // Setting up input parsing variables
    for (bool & t : m_tdmap) t = false;
    for (auto & c : " \t\r\n\a") m_tdmap[static_cast<int>(c)] = true;

    // Setting up LDAP-themed items
    m_user = getlogin();
    m_systems = CritSysContainer();
    m_ldap = new LDAPClient();

    try {
        m_ldap->connect();
        for (const auto& group : m_ldap->getRemoteAccessGroups()) {
            m_systems.addAll(m_ldap->getAccessibleSystems(m_user, group));
        }
        m_ldap->disconnect();
    }
    catch(...) {
        throw;
    }

    for (auto& system : m_systems.getNames()) {
        addCommand(ShellCommand(system, "Redirects to system <" + system + ">",
            [=] (StringVec p) {
                unused(p);

                auto sys = m_systems.get(system);

                out("You are about to be redirected to [" + system + "]");
                out("System description: " + sys.getDesc());
                std::cout << "Do you with to proceed? (y/n)  ";

                const auto line = getInputLine();
                if (line.size() == 1 && (line.at(0) == "y" || line.at(0) == "Y")) {
                    out("Redirecting... TO BE DONE");
                    debug("DEBUG: IP=" + sys.getAddr());
                }
                else {
                    out("Redirection aborted.");
                }

                return true;
            }
        ));
    }

    this->greet();
    this->setupCustomCommands();

}


Shell::~Shell() = default;


void Shell::addCommand(ShellCommand cmd) {
    m_commands.insert(std::make_pair(cmd.getName(), cmd));
}


Retval Shell::call(const String &commandName, StringVec parameters) {

    auto cmd = m_commands.find(commandName);
    if (cmd != m_commands.end()) {
        return cmd->second(parameters) ? Retval::SUCCESS : Retval::FAILURE;
    }
    else {
        return Retval::NOT_FOUND;
    }

}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---


StringVec Shell::getInputLine() {

    String line;
    StringVec res;
    String token;

    std::getline(std::cin, line);

    for (auto &c : line) {

        // iterate over each char and check for delimiter
        if (c >= 0 && m_tdmap[static_cast<int>(c)]) {
            if (!token.empty()) {
                res.push_back(token);
                token.clear();
            }
        }
        else {
            token += c;
        }
    }

    if (!token.empty()) {
        res.push_back(token);
    }

    return res;

}


void Shell::loop() {

    int status;
    do {

        std::cout << ":> ";

        StringVec line = getInputLine();
        if (line.empty()) continue;

        String cmd = line.at(0);
        line.erase(line.begin());

        status = execute(cmd, line);
//        out(cmd + " returned " + str(status));
//        std::cout << "<<" << cmd << ">> ";
//        for (const auto& item : line) {
//            std::cout << "[" << item << "] ";
//        }
//        out("");

    } while (status);

}


int Shell::execute(const String& commandName, const StringVec& parameters) {

    Retval r = call(commandName, parameters);
    if (r == Retval::NOT_FOUND) {
        // call external bash shell, let's go low
        String assembled = String(commandName) + " ";
        for (auto& p : parameters) {
            assembled += String(p) + " ";
        }

        // https://www.geeksforgeeks.org/system-call-in-c/
        // TODO: is int system(char*) viable?
        system(assembled.c_str());
        return Retval::SUCCESS;

    }
    return r;

}


void Shell::greet() {

    out("  ------------------------------------------------------------------------------");
    out("  | Welcome to jump server, " + m_user);
    out("  | This is jump shell from project for Telenor, created by NDS 8");
    out("  | ----------------------------------------------------------------------------");
    out("  | Please choose the specified critical system you are authorised to access:");

    auto names = m_systems.getNames();
    if (names.empty()) {
        out ("  | <!> None systems available. Please contact your administrator.");
    }
    else {
        for (auto &sys : m_systems.getNames()) {
            out("  | <" + sys + ">");
        }
    }

    out("  ------------------------------------------------------------------------------");

}


void Shell::setupCustomCommands() {

    String quitDesc = "Disconnects from the jumpserver";
    addCommand(ShellCommand("exit", quitDesc, [](StringVec p) { unused(p); return false; }));
    addCommand(ShellCommand("quit", quitDesc, [](StringVec p) { unused(p); return false; }));

    addCommand(ShellCommand("help", "Prints available commands",
        [=] (StringVec p) {
            unused(p);

            out("NDS8 JumpShell");
            out("Please type the number corresponding to the system you want to access, and hit enter.");
            out("The following commands are built in / available:");
            for (auto pair : this->m_commands) {
                out("  " + pair.second.getName() + " : " + pair.second.getDesc());
            }
            out("Use the man command for information on other programs.");
            return true;
        }
    ));

    addCommand(ShellCommand("debug", "argument: the name of critical system",
        [=] (StringVec p) {
            try {
                auto sys = m_systems.get(p.at(0));
                out("Printing debug info about: " + sys.getName());
                out("IP: " + sys.getAddr());
                out("Desc: " + sys.getDesc());
            }
            catch(std::invalid_argument &e) {
                out(e.what());
            }

            return true;
        }
    ));

//    addCommand(ShellCommand("1",
//        [] (StringVec p) {
//            unused(p);
//            out("::::::::::::::::::: START OF DEBUGGING MESSAGES :::::::::::::::::::");
//            out("Checking user validity on system [1]");
//            QueryResult result = LDAPClient().verify("coffe-access");
//            String r = result ? "VERIFIED" : "REJECTED";
//            out("Shellcommand 2: result is: " + r);
//            out(":::::::::::::::::::: END OF DEBUGGING MESSAGES ::::::::::::::::::::");
//            // out("You are going to be redirected to System A");
//            //FILE *f = popen( "ssh -t -t nds@192.168.40.1 -p 50222", "r" );
//            return false;
//        }
//    ));

}
