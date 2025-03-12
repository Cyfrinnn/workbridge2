package com.example.last

import okhttp3.*
import java.io.IOException

object NetworkUtils {
    private val client = OkHttpClient()

    fun post(url: String, params: Map<String, String>, callback: Callback) {
        val formBody = FormBody.Builder().apply {
            params.forEach { (key, value) ->
                add(key, value)
            }
        }.build()

        val request = Request.Builder()
            .url(url)
            .post(formBody)
            .build()

        client.newCall(request).enqueue(callback)
    }
}
