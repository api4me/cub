package main

import (
    "log"
    "net/http"
    "strconv"
    "runtime/debug"
    "github.com/hoisie/redis"
)
/*{{{ safeHandler */
func safeHandler(fn http.HandlerFunc) http.HandlerFunc {
    return func(w http.ResponseWriter, r *http.Request) {
        defer func() {
            if e, ok := recover().(error); ok {
                http.Error(w, e.Error(), http.StatusInternalServerError)

                log.Println("WARN: panic in %v - %v", fn, e)
                log.Println(string(debug.Stack()))
            }
        }()
        fn(w, r)
    }
}
/*}}}*/
/*{{{ payHandler */
func payHandler(w http.ResponseWriter, r *http.Request) {
    uid := r.FormValue("uid")
    cid := r.FormValue("cid")
    max := r.FormValue("max")
    key := "uid" + uid + "~cid" + cid

    var client redis.Client
    val, _ := client.Get(key)
    if string(val) < max {
        client.Incr(key)
        client.Sadd("suid" + uid, []byte(cid))
    }
    w.Write(val)
}
/*}}}*/
/*{{{ getPayHandler */
func getPayHandler(w http.ResponseWriter, r *http.Request) {
    uid := r.FormValue("uid")
    key := "suid" + uid

    var client redis.Client
    val, _ := client.Smembers(key)
    for i := 0; i < len(val); i++ {
        if i > 0 {
            w.Write([]byte(","))
        }
        w.Write(val[i])
    }
}
/*}}}*/
/*{{{ payLimitHandler */
func payLimitHandler(w http.ResponseWriter, r *http.Request) {
    uid := r.FormValue("uid")
    cid := r.FormValue("cid")
    max := r.FormValue("max")
    key := "uid" + uid + "~cid" + cid

    var client redis.Client
    val, _ := client.Get(key)
    if  string(val) > max {
        w.Write([]byte("0"));
    }
    m, _ := strconv.Atoi(max)
    v, _ := strconv.Atoi(string(val))
    w.Write([]byte(strconv.Itoa(m - v)))
}
/*}}}*/
/*{{{ main */
func main() {
    http.HandleFunc("/pay", safeHandler(payHandler))
    http.HandleFunc("/getpay", safeHandler(getPayHandler))
    http.HandleFunc("/paylimit", safeHandler(payLimitHandler))

    err := http.ListenAndServe(":6060", nil)
    if err != nil {
        log.Fatal("ListenAndServe: ", err.Error())
    }
/*}}}*/
}
